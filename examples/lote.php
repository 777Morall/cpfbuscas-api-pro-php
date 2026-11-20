<?php
declare(strict_types=1);
require __DIR__.'/../bootstrap.php';
if ($argc!==2) throw new InvalidArgumentException('Uso: php examples/lote.php tarefas.jsonl');

$input=new SplFileObject($argv[1],'r');
$api=client();
$line=0;
while (!$input->eof()) {
    $raw=trim($input->fgets()); $line++;
    if ($raw==='') continue;
    try {
        $task=json_decode($raw,true,512,JSON_THROW_ON_ERROR);
        if (!is_array($task) || !is_string($task['module'] ?? null) || !is_array($task['params'] ?? null)) throw new InvalidArgumentException('Tarefa inválida.');
        if (($task['module'] ?? '')==='veiculos_placamassa') throw new InvalidArgumentException('Fila aceita somente GET; POST requer conferência manual.');
        $result=$api->query($task['module'],$task['params']);
        echo json_encode(['line'=>$line,'result'=>$result],JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR).PHP_EOL;
        
    } catch (Throwable $e) {
        fwrite(STDERR,'Falha na linha '.$line.'. Revise antes de retomar.'.PHP_EOL);
        throw $e;
    }
}
