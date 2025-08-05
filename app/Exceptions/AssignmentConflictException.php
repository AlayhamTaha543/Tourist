<?php
namespace App\Exceptions;
class AssignmentConflictException extends \Exception
{
    protected $message = 'Assignment conflict detected';
}
