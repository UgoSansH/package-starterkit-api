<?php

namespace AppBundle\Definition;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Definition
 */
class Definition
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var Yaml
     */
    protected $parser;

    /**
     * @var array
     */
    protected $config;

    /**
     * Construct
     *
     * @param Yaml   $parser
     * @param string $path
     */
    public function __construct(Yaml $parser, $path)
    {
        $this->parser = $parser;
        $this->path   = $path;
        $this->config = null;
    }

    /**
     * load config
     *
     * @return array
     * @throws ParseException
     */
    protected function load()
    {
        if (!is_array($this->config)) {

            $params = $this->parser->parse($this->getFileContent());

            if (!array_key_exists('parameters', $params)) {
                throw new ParseException('Invalid root node, expected "parameters"', 1);
            }

            $this->config = !empty($params['parameters']) ? $params['parameters'] : [];
        }

        return $this->config;
    }

    protected function getFileContent()
    {
        if (!is_file($this->path)) {
            throw new DefinitionException(sprintf('Definition file not found "%s"', $this->path), 1);
        }

        if (!is_readable($this->path)) {
            throw new DefinitionException(sprintf('Definition file is not readdable "%s"', $this->path), 2);
        }

        if (!$content = file_get_contents($this->path)) {
            throw new DefinitionException(sprintf('Failed to read Definition file content "%s"', $this->path), 3);
        }

        return $content;
    }

    /**
     * all
     *
     * @return array
     */
    public function all()
    {
        $this->load();

        return $this->config;
    }

    /**
     * Get keys
     *
     * @param string $anem
     *
     * @return array|null
     */
    public function get($name)
    {
        return $this->has($name) ? $this->config[$name] : null;
    }

    /**
     * has
     *
     * @param string $name
     *
     * @return boolean
     */
    public function has($name)
    {
        $this->load();

        return array_key_exists($name, $this->config);
    }

}
