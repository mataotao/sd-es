<?php

namespace App\Http\Design\Infrastructure\Helper;


use App\Http\Controllers\Manager\LogController;

class Scheduler
{
    protected $maxTaskId       = 0;
    protected $taskMap         = []; // taskId => task
    protected $taskQueue;
    protected $waitingForRead  = [];
    protected $waitingForWrite = [];
    
    public function __construct()
    {
        $this->taskQueue = new \SplQueue();
        
    }
    
    public function newTask(\Generator $coroutine)
    {
        $tid                 = ++$this->maxTaskId;
        $task                = new Task($tid, $coroutine);
        $this->taskMap[$tid] = $task;
        $this->schedule($task);
        
        return $tid;
    }
    
    public function schedule(Task $task)
    {
        $this->taskQueue->enqueue($task);
    }
    
    public function run()
    {
        $this->newTask($this->ioPollTask());
        
        while (!$this->taskQueue->isEmpty()) {
            $task   = $this->taskQueue->dequeue();
            $retval = $task->run();
            
            if ($retval instanceof SystemCall) {
                $retval($task, $this);
                continue;
            }
            
            if ($task->isFinished()) {
                unset($this->taskMap[$task->getTaskId()]);
            } else {
                $this->schedule($task);
            }
        }
    }
    
    public function killTask($tid)
    {
        if (!isset($this->taskMap[$tid])) {
            return false;
        }
        
        unset($this->taskMap[$tid]);
        
        foreach ($this->taskQueue as $i => $task) {
            if ($task->getTaskId() === $tid) {
                unset($this->taskQueue[$i]);
                break;
            }
        }
        
        return true;
    }
    
    public function waitForRead($socket, Task $task)
    {
        if (isset($this->waitingForRead[(int)$socket])) {
            $this->waitingForRead[(int)$socket][1][] = $task;
        } else {
            $this->waitingForRead[(int)$socket] = [
                $socket,
                [$task],
            ];
        }
    }
    
    public function waitForWrite($socket, Task $task)
    {
        if (isset($this->waitingForWrite[(int)$socket])) {
            $this->waitingForWrite[(int)$socket][1][] = $task;
        } else {
            $this->waitingForWrite[(int)$socket] = [
                $socket,
                [$task],
            ];
        }
    }
    
    protected function ioPoll($timeout)
    {
        $rSocks = [];
        foreach ($this->waitingForRead as list($socket)) {
            $rSocks[] = $socket;
        }
        
        $wSocks = [];
        foreach ($this->waitingForWrite as list($socket)) {
            $wSocks[] = $socket;
        }
        
        $eSocks = []; // dummy
        
        if (@!stream_select($rSocks, $wSocks, $eSocks, $timeout)) {
            return;
        }
        
        foreach ($rSocks as $socket) {
            list(, $tasks) = $this->waitingForRead[(int)$socket];
            unset($this->waitingForRead[(int)$socket]);
            
            foreach ($tasks as $task) {
                $this->schedule($task);
            }
        }
        
        foreach ($wSocks as $socket) {
            list(, $tasks) = $this->waitingForWrite[(int)$socket];
            unset($this->waitingForWrite[(int)$socket]);
            
            foreach ($tasks as $task) {
                $this->schedule($task);
            }
        }
    }
    
    protected function ioPollTask()
    {
        while (true) {
            if ($this->taskQueue->isEmpty()) {
                $this->ioPoll(null);
            } else {
                $this->ioPoll(0);
            }
            yield;
        }
    }
}


class Task
{
    protected $taskId;
    protected $coroutine;
    protected $sendValue        = null;
    protected $beforeFirstYield = true;
    
    public function __construct($taskId, \Generator $coroutine)
    {
        $this->taskId    = $taskId;
        $this->coroutine = StackedCoroutine($coroutine);
    }
    
    public function getTaskId()
    {
        return $this->taskId;
    }
    
    public function setSendValue($sendValue)
    {
        $this->sendValue = $sendValue;
    }
    
    public function run()
    {
        if ($this->beforeFirstYield) {
            $this->beforeFirstYield = false;
            
            return $this->coroutine->current();
        } else {
            $retval          = $this->coroutine->send($this->sendValue);
            $this->sendValue = null;
            
            return $retval;
        }
    }
    
    public function isFinished()
    {
        return !$this->coroutine->valid();
    }
}


class SystemCall
{
    protected $callback;
    
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }
    
    public function __invoke(Task $task, Scheduler $scheduler)
    {
        $callback = $this->callback;
        
        return $callback($task, $scheduler);
    }
}

class CoroutineReturnValue
{
    protected $value;
    
    public function __construct($value)
    {
        $this->value = $value;
    }
    
    public function getValue()
    {
        return $this->value;
    }
}


class CoSocket
{
    protected $socket;
    
    public function __construct($socket)
    {
        $this->socket = $socket;
    }
    
    public function accept()
    {
        yield waitForRead($this->socket);
        yield retval(new CoSocket(stream_socket_accept($this->socket, 0)));
    }
    
    public function read($size)
    {
        yield waitForRead($this->socket);
        yield retval(fread($this->socket, $size));
    }
    
    public function write($string)
    {
        yield waitForWrite($this->socket);
        fwrite($this->socket, $string);
    }
    
    public function close()
    {
        @fclose($this->socket);
    }
}


function getTaskId()
{
    return new SystemCall(function (Task $task, Scheduler $scheduler) {
        $task->setSendValue($task->getTaskId());
        $scheduler->schedule($task);
    });
}

function newTask(\Generator $coroutine)
{
    return new SystemCall(function (Task $task, Scheduler $scheduler) use ($coroutine) {
        $task->setSendValue($scheduler->newTask($coroutine));
        $scheduler->schedule($task);
    });
}

function killTask($tid)
{
    return new SystemCall(function (Task $task, Scheduler $scheduler) use ($tid) {
        $task->setSendValue($scheduler->killTask($tid));
        $scheduler->schedule($task);
    });
}

function waitForRead($socket)
{
    return new SystemCall(function (Task $task, Scheduler $scheduler) use ($socket) {
        $scheduler->waitForRead($socket, $task);
    });
}

function waitForWrite($socket)
{
    return new SystemCall(function (Task $task, Scheduler $scheduler) use ($socket) {
        $scheduler->waitForWrite($socket, $task);
    });
}

function server($ip, $port)
{
    
    
    $socket = @stream_socket_server("tcp://$ip:$port", $errNo, $errStr);
    
    if (!$socket) throw new \Exception($errStr, $errNo);
    
    //stream_set_blocking($socket, 0);
    
    $socket = new CoSocket($socket);
    while (true) {
        yield newTask(handleClient(yield $socket->accept()));
    }
}

function handleClient($socket)
{
    $data = (yield $socket->read(8192));
    
    $msg       = "Received following request:\n\n$data";
    $msgLength = strlen($msg);
    $response  = <<<RES
HTTP/1.1 200 OK\r
Content-Type: text/plain\r
Content-Length: $msgLength\r
Connection: close\r
\r
$msg
RES;
    
    yield $socket->write($response);
    yield $socket->close();
}

function retval($value)
{
    return new CoroutineReturnValue($value);
}

function StackedCoroutine(\Generator $gen)
{
    $stack = new \SplStack;
    
    for (; ;) {
        $value = $gen->current();
        
        if ($value instanceof \Generator) {
            $stack->push($gen);
            $gen = $value;
            continue;
        }
        
        $isReturnValue = $value instanceof CoroutineReturnValue;
        if (!$gen->valid() || $isReturnValue) {
            if ($stack->isEmpty()) {
                return;
            }
            
            $gen = $stack->pop();
            $gen->send($isReturnValue ? $value->getValue() : null);
            continue;
        }
        
        $gen->send(yield $gen->key() => $value);
    }
}

///////////////////////////////////////////////////////////////////////////////////
function log_server($ip, $port)
{
    $socket = @stream_socket_server("tcp://$ip:$port", $errNo, $errStr);
    
    if (!$socket) throw new \Exception($errStr, $errNo);
    
    //stream_set_blocking($socket, 0);
    
    $socket = new CoSocket($socket);
    while (true) {
        yield newTask(log_handle_client(yield $socket->accept()));
    }
}

function log_handle_client($socket)
{
    $data = (yield $socket->read(8192));
    
    $msg       = "Received following request:\n\n$data";
    $msgLength = strlen($msg);
    $data      = json_decode($data, true);
    if ($data['logType'] == 'success') {
        LogController::log($data, $data['site']);
    } else {
        LogController::error($data, $data['site']);
    }
    $response = <<<RES
HTTP/1.1 200 OK\r
Content-Type: text/plain\r
Content-Length: $msgLength\r
Connection: close\r
\r
$msg
RES;
    
    yield $socket->write($response);
    yield $socket->close();
}