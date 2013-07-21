<?php

/**
 * Not a programmer error. Invalid user input.
 * So don't log this exception with ERROR level.
 */
class ValidationException extends Exception {
}

?>