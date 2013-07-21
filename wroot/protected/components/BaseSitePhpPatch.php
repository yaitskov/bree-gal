<?php

abstract class BaseSitePhpPatch {
    /**
     * Alters repository of project files
     * @params resource db connection
     */
    abstract public function apply($connection);

    public function assertTrueStop($cond, $message = '') {
        if (!$cond) {
            throw new StopUpdateException($message);
        }
    }

}

?>