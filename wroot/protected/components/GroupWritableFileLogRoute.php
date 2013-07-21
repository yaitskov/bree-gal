<?php

/**
 *  Cron jobs are launched from other user.
 */
class GroupWritableFileLogRoute extends CFileLogRoute {
  /**
   * File log permissions.
   */
  public $perms = 0664;

  protected function processLogs($logs) {
    parent::processLogs($logs);
    $logFile=$this->getLogPath().DIRECTORY_SEPARATOR.$this->getLogFile();
    @chmod($logFile, $this->perms);
  }
}
