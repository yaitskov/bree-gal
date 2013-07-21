<?php

class ConsoleLogRoute extends CLogRoute {
    protected function processLogs($logs) {
        foreach ($logs as $log) {
			echo $this->formatLogMessage($log[0],$log[1],$log[2],$log[3]);
        }
    }
}

?>