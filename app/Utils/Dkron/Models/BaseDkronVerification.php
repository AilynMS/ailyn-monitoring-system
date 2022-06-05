<?php

namespace App\Utils\Dkron\Models;

use App\Utils\Dkron\Dkron;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

abstract class BaseDkronVerification extends Model
{

    /** @var \App\Utils\Dkron\Dkron */
    protected $dkronClient;

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saved(function ($verification) {
            // Create/update job
            $verification->createJob();
        });

        static::deleted(function ($verification) {
            //Delete Job
            try {
                $verification->deleteJob();
            } catch (Exception $e) {
                Log::error('Error borrando job en una verificación ' .  $e->getMessage(), $verification->toArray());
            }
        });
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->dkronClient = new Dkron;
    }

    /**
     * Creates a new job in dkron
     *
     * @param array $data Any extra data
     */
    public function createJob($data = [])
    {
        $executor_data = [
            'method' => 'POST',
            'headers' => '["Content-Type: application/json"]',
            'url' => $this->getJobUrl(),
            'body' => $this->getJobPayload(),
            'expectCode' => '200',
        ];

        $static_job_data = [
            'name' => $this->getUniqueJobIdentifier(),
            'schedule' => $this->getJobSchedule(),
            'timezone' => $this->getJobTimezone(),
            'executor' => $this->getJobExecutorType(),
            'disabled' => $this->isDisabled(),
            'executor_config' => $executor_data,
        ];

        $real_data = array_merge($static_job_data, $data);

        $this->dkronClient->createOrUpdateJob($real_data);
    }

    /**
     * Deletes job
     */
    public function deleteJob()
    {
        $this->dkronClient->deleteJob($this->getUniqueJobIdentifier());
    }

    /**
     * Run job
     *
     * @return bool If the job was successful
     */
    public function runJob()
    {
        try {
            $this->dkronClient->runJob($this->getUniqueJobIdentifier());

            return true;
        } catch (Exception $e) {
            Log::error('Error ejecutando un job en una verificación ' .  $e->getMessage(), $this->toArray());

            return false;
        }
    }

    /**
     * Gets job url (external microservice url)
     *
     * @return string
     */
    public function getJobUrl()
    {
        return config('microservices.verifications.url') . '/' . $this->getVerificationType();
    }

    /**
     * Gets the job specific payload in a json string format
     *
     *
     * @return string
     */
    abstract public function getJobPayload();

    /**
     * Gets the verification type
     *
     * @return string
     */
    abstract public function getVerificationType();


    /**
     * Get measurement name
     * @return string
     */
    abstract public function getMeasurementName();

    /**
     * Get the unique job identifier
     *
     * @return string
     */
    public function getUniqueJobIdentifier()
    {
        return Str::lower($this->token);
    }

    /**
     * Gets the job schedule (interval)
     *
     * @return string
     */
    public function getJobSchedule()
    {
        $interval = $this->interval;

        return "@every {$interval}m";
    }

    /**
     * Gets the job timezone
     *
     * @return string
     */
    public function getJobTimezone()
    {
        return session('timezone');
    }

    /**
     * Gets the job executor, defaults to HTTP
     *
     * @return string
     */
    public function getJobExecutorType()
    {
        return 'http';
    }

    /**
     * Check if the verifiation is disabled
     *
     * @return bool
     */
    public function isDisabled()
    {
        return ! $this->status;
    }
}
