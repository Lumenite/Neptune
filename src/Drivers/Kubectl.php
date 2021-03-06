<?php

namespace Lumenite\Neptune\Drivers;

use Illuminate\Filesystem\Filesystem;
use Lumenite\Neptune\Exceptions\ResourceDeploymentException;
use Lumenite\Neptune\Process;
use Lumenite\Neptune\ResourceResponse\ClusterResponse;
use Lumenite\Neptune\Resources\Resource;
use Lumenite\Neptune\Resources\ResourceContract;

/**
 * Kubectl process dispatcher class.
 *
 * @package Modelizer\Lanton
 */
class Kubectl implements DriverContract
{
    /** @var Filesystem $fileManager */
    protected $fileManager;

    /** @var ClusterResponse $response */
    protected $response;

    /**
     * @param Filesystem $fileManager
     */
    public function __construct(Filesystem $fileManager)
    {
        $this->fileManager = $fileManager;
    }

    /**
     * @param \Lumenite\Neptune\Resources\Resource $resource
     * @param callable|null $callback
     * @return \Lumenite\Neptune\ResourceResponse\ClusterResponse
     * @throws \Lumenite\Neptune\Exceptions\ResourceDeploymentException
     */
    public function get(Resource $resource, callable $callback = null)
    {
        $this->useContext($resource)->verifyResource($resource);

        $process = new Process(
            "kubectl get {$resource->getKind()} {$resource->getName()} -n {$resource->getNamespace()} -o json"
        );

        $process->run($this->handleOutput($resource, $callback));

        return $this->response;
    }

    /**
     * @param \Lumenite\Neptune\Resources\Resource $resource
     * @param callable|null $callback
     * @return \Lumenite\Neptune\ResourceResponse\ClusterResponse
     * @throws \Lumenite\Neptune\Exceptions\ResourceDeploymentException
     */
    public function apply(Resource $resource, callable $callback = null)
    {
        $this->useContext($resource)->verifyResource($resource);

        $process = new Process("kubectl apply -o json -f {$resource->getFilePath()}");

        $process->run($this->handleOutput($resource, $callback));

        return $this->response;
    }

    /**
     * @param \Lumenite\Neptune\Resources\Resource $resource
     * @param callable|null $callback
     * @return \Lumenite\Neptune\ResourceResponse\ClusterResponse
     * @throws \Lumenite\Neptune\Exceptions\ResourceDeploymentException
     */
    public function delete(Resource $resource, callable $callback = null)
    {
        $this->useContext($resource)->verifyResource($resource);

        $process = new Process("kubectl delete -f {$resource->getFilePath()}");

        $process->run($this->handleOutput($resource, $callback));

        return $this->response;
    }

    /**
     * @param \Lumenite\Neptune\Resources\Resource $resource
     * @param callable|null $callback
     * @return $this
     * @throws \Lumenite\Neptune\Exceptions\ResourceDeploymentException
     */
    public function wait(Resource $resource, callable $callback = null)
    {
        if ($this->get($resource, $callback)->isPending()) {
            sleep(1.5);

            return $this->wait($resource, $callback);
        }

        return $this;
    }

    /**
     * @param \Lumenite\Neptune\Resources\Resource $resource
     * @param string $container
     * @param callable|null $callback
     * @return \Lumenite\Neptune\Process
     * @throws \Lumenite\Neptune\Exceptions\ResourceDeploymentException
     */
    public function logs(Resource $resource, string $container, ?callable $callback)
    {
        $this->useContext($resource)->verifyResource($resource);

        $process = new Process(sprintf(
            'kubectl logs -n %s -l job-name=%s -c %s -f --pod-running-timeout=20s',
            $resource->getNamespace(),
            $resource->getName(),
            $container
        ));

        try {
            $process->run($this->handleOutput($resource, $callback, "<fg=green>$container: </>"));
        } catch (ResourceDeploymentException $exception) {
            if (!strpos($exception->getMessage(), 'is waiting to start: PodInitializing')) {
                throw $exception;
            }

            sleep(1);
            $callback('Please wait for the container to get initialize...');
            $this->logs($resource, $container, $callback);
        }

        return $process;
    }

    /**
     * @return \Lumenite\Neptune\ResourceResponse\ClusterResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param \Lumenite\Neptune\Resources\ResourceContract $resource
     * @param callable|null $callback
     * @param string $prefixStdout
     * @return \Closure
     */
    protected function handleOutput(ResourceContract $resource, callable $callback = null, $prefixStdout = '')
    {
        return function ($status, $stdout) use ($resource, $callback, $prefixStdout) {
            $stdout = $prefixStdout . $stdout;
            if ($status !== Process::OUT) {
                throw new ResourceDeploymentException($stdout);
            }

            if (is_array($response = json_decode($stdout, true))) {
                $responseClass = $resource->getResponseClass();
                $this->response = new $responseClass($response);
            }

            if (is_callable($callback)) {
                return $callback($stdout, $this->response);
            }

            return false;
        };
    }

    /**
     * @param \Lumenite\Neptune\Resources\Resource $resource
     * @throws \Lumenite\Neptune\Exceptions\ResourceDeploymentException
     */
    protected function verifyResource(Resource $resource)
    {
        if (!$resource->getFilePath()) {
            throw new ResourceDeploymentException('Resource not loaded before applying');
        }
    }

    /**
     * @param \Lumenite\Neptune\Resources\Resource $resource
     * @return $this
     */
    protected function useContext(Resource $resource)
    {
        $process = new Process("kubectl config use-context {$resource->getContext()}");
        $process->run();

        return $this;
    }
}
