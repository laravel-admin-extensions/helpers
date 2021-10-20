<?php

namespace Encore\Admin\Helpers\Scaffold;

class ControllerCreator
{
    /**
     * Controller full name.
     *
     * @var string
     */
    protected $name;

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    protected $DummyGridField = '';

    protected $DummyShowField = '';

    protected $DummyFormField = '';

    /**
     * ControllerCreator constructor.
     *
     * @param string $name
     * @param null   $files
     */
    public function __construct($name, $files = null)
    {
        $this->name = $name;

        $this->files = $files ?: app('files');
    }

    /**
     * Create a controller.
     *
     * @param string $model
     *
     * @throws \Exception
     *
     * @return string
     */
    public function create($model, $fields)
    {
        $path = $this->getpath($this->name);

        if ($this->files->exists($path)) {
            throw new \Exception("Controller [$this->name] already exists!");
        }

        $this->generateGridField($fields);

        $this->generateShowField($fields);

        $this->generateFormField($fields);

        $stub = $this->files->get($this->getStub());

        $this->files->put($path, $this->replace($stub, $this->name, $model));

        return $path;
    }

    /**
     * @param string $stub
     * @param string $name
     * @param string $model
     *
     * @return string
     */
    protected function replace($stub, $name, $model)
    {
        $stub = $this->replaceClass($stub, $name);

        return str_replace(
            ['DummyModelNamespace', 'DummyModel', 'DummyGridField', 'DummyShowField', 'DummyFormField'],
            [$model, class_basename($model), $this->DummyGridField, $this->DummyShowField, $this->DummyFormField],
            $stub
        );
    }

    /**
     * Get controller namespace from giving name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param string $stub
     * @param string $name
     *
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        return str_replace(['DummyClass', 'DummyNamespace'], [$class, $this->getNamespace($name)], $stub);
    }

    /**
     * Get file path from giving controller name.
     *
     * @param $name
     *
     * @return string
     */
    public function getPath($name)
    {
        $segments = explode('\\', $name);

        array_shift($segments);

        return app_path(implode('/', $segments)).'.php';
    }

    /**
     * Get stub file path.
     *
     * @return string
     */
    public function getStub()
    {
        return (getenv("ADMIN_STUBS_DIR") || __DIR__.'/stubs/'). 'controller.stub';

    }

    public function generateFormField($fields = [])
    {
        $fields = array_filter($fields, function ($field) {
            return isset($field['name']) && !empty($field['name']);
        });

        if (empty($fields)) {
            throw new \Exception('Table fields can\'t be empty');
        }

        foreach ($fields as $field) {
            $rows[] = "\$form->text('{$field['name']}', '{$field['name']}');\n";
        }

        $this->DummyFormField = trim(implode(str_repeat(' ', 8), $rows), "\n");

        return $this;
    }

    public function generateShowField($fields = [])
    {
        $fields = array_filter($fields, function ($field) {
            return isset($field['name']) && !empty($field['name']);
        });

        if (empty($fields)) {
            throw new \Exception('Table fields can\'t be empty');
        }
        foreach ($fields as $field) {
            $rows[] = "\$show->{$field['name']}('{$field['name']}');\n";
        }

        $this->DummyShowField = trim(implode(str_repeat(' ', 8), $rows), "\n");

        return $this;
    }

    public function generateGridField($fields = [])
    {
        $fields = array_filter($fields, function ($field) {
            return isset($field['name']) && !empty($field['name']);
        });

        if (empty($fields)) {
            throw new \Exception('Table fields can\'t be empty');
        }
        foreach ($fields as $field) {
            $rows[] = "\$grid->{$field['name']}('{$field['name']}');\n";
        }

        $this->DummyGridField = trim(implode(str_repeat(' ', 8), $rows), "\n");

        return $this;
    }
}
