<?php

namespace App\Utils\Dkron;

use Dkron\Api;
use Dkron\Models\Job;

/**
 * Dkron helper utility wrapper
 */
class Dkron
{
    /** @var \Dkron\Api */
    protected $client;

    /**
     * Creates a new dkron helper instance
     *
     * @param null|string $url
     */
    public function __construct($url = null)
    {
        $url = $url ?? config('dkron.url');
        $this->client = new Api($url);
    }

    /**
     * Creates or update a job from an array
     *
     * @param array $data
     *
     */
    public function createOrUpdateJob($data)
    {
        $this->client->saveJob(Job::createFromArray($data));
    }

    /**
     * Deletes a job by name
     *
     * @param string $name
     */
    public function deleteJob($name)
    {
        $this->client->deleteJob($name);
    }

    /**
     * Runs a job by name
     *
     * @param string $name
     */
    public function runJob($name)
    {
        $this->client->runJob($name);
    }
}
