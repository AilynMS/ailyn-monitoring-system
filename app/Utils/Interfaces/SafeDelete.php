<?php

namespace App\Utils\Interfaces;

/**
 * Interface to check if it is safe to delete a resource
 */
interface SafeDelete
{
    /**
     * @return bool
     */
    public function isDeleteable();
}
