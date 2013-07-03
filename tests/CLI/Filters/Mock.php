<?php

namespace go\Tests\Request\CLI\Filters;

class Mock extends \go\Request\CLI\Filters\Base
{
    protected $errorMessage = 'Option {{ option }} is not valid';

    public function process()
    {
        if (empty($this->params['valid'])) {
            if (!isset($this->params['value'])) {
                return $this->error();
            }
            if ($this->value !== $this->params['value']) {
                return $this->error('Invalid value for {{ option }}');
            }
        }
        if (isset($this->params['new'])) {
            $this->value = $this->params['new'];
        }
        return true;
    }

    public function getOption()
    {
        return $this->option;
    }

    public function getParams()
    {
        return $this->params;
    }
}
