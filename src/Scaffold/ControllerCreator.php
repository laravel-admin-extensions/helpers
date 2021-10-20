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
     * @var string
     */
    protected $listForm = '';
    protected $listShow = '';
    protected $listGrid = '';

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
     * Set Type of Fileds.
     *
     * @param string $dbTypes
     *
     * @return string
     */
    public function setFieldType($dbTypes)
    {
        $numberTypes = [
            'integer', 'float', 'double', 'decimal', 'tinyInteger', 'smallInteger',
            'mediumInteger', 'bigInteger', 'unsignedTinyInteger', 'unsignedSmallInteger', 'unsignedMediumInteger',
            'unsignedInteger', 'unsignedBigInteger',
        ];

        $textareaTypes = [
            'text',  'mediumText', 'longText',
        ];

        $dateTypes = [
            'date',
        ];

        $timeTypes = [
            'time', 'timeTz',
        ];

        $dateTimeTypes = [
            'dateTime', 'dateTimeTz',
        ];

        $boolTypes = [
            'boolean',
        ];

        $enumTypes = [
            'enum',
        ];

        $ipTypes = [
            'ipAddress',
        ];

        if (in_array($dbTypes, $numberTypes)) {
            $field = 'number';
        } elseif (in_array($dbTypes, $textareaTypes)) {
            $field = 'texatrea';
        } elseif (in_array($dbTypes, $dateTypes)) {
            $field = 'date';
        } elseif (in_array($dbTypes, $timeTypes)) {
            $field = 'time';
        } elseif (in_array($dbTypes, $dateTimeTypes)) {
            $field = 'datetime';
        } elseif (in_array($dbTypes, $boolTypes)) {
            $field = 'switch';
        } elseif (in_array($dbTypes, $enumTypes)) {
            $field = 'radio';
        } elseif (in_array($dbTypes, $ipTypes)) {
            $field = 'ip';
        } else {
            $field = 'text';
        }

        return $field;
    }

    /**
     * Build the List of Fileds.
     *
     * @param array     $fields
     * @param string    $keyName
     * @param bool|true $useTimestamps
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function buildFields($fields = [], $keyName = 'id', $useTimestamps = true)
    {
        $fields = array_filter($fields, function ($field) {
            return isset($field['name']) && !empty($field['name']);
        });

        if (empty($fields)) {
            throw new \Exception('Fields can\'t be empty');
        }

        $grid[] = "\$grid->$keyName('$keyName')->sortable();\n";
        $show[] = "\$show->$keyName('$keyName');\n";
        $form[] = "\$form->display('$keyName'); \n";
        foreach ($fields as $field) {
            $column = "->{$field['name']}('{$field['name']}')";
            if ($this->setFieldType($field['type']) === 'date') {
                $colgrid = $column."->display(function(\$date){ \n ".
                    "           return Carbon::parse(\$date)->translatedFormat('d F Y'); \n ".
                    '       })';
                $colshow = $column."->as(function(\$date){ \n ".
                    "           return Carbon::parse(\$date)->translatedFormat('d F Y'); \n ".
                    '       })';
            } elseif ($this->setFieldType($field['type']) === 'datetime') {
                $colgrid = $column."->display(function(\$date){ \n ".
                    "           return Carbon::parse(\$date)->translatedFormat('d F Y H:m:s'); \n ".
                    '       })';
                $colshow = $column."->as(function(\$date){ \n ".
                    "           return Carbon::parse(\$date)->translatedFormat('d F Y H:m:s'); \n ".
                    '       })';
            } else {
                $colgrid = $column;
                $colshow = $column;
            }

            $grid[] = '$grid'.$colgrid.";\n";
            $show[] = '$show'.$colshow.";\n";
            $form[] = '$form->'.$this->setFieldType($field['type'])."('{$field['name']}');\n";
        }

        if ($useTimestamps) {
            $show[] = "\$show->created_at(trans('admin.created_at'))->as(function (\$created_at) { \n";
            $show[] = '   '." return Carbon::parse(\$created_at)->translatedFormat('d F Y H:m:s'); \n";
            $show[] = "}); \n";
            $show[] = "\$show->updated_at(trans('admin.updated_at'))->as(function (\$updated_at) { \n";
            $show[] = '   '." return Carbon::parse(\$updated_at)->translatedFormat('d F Y H:m:s'); \n";
            $show[] = "}); \n";
            $grid[] = "\$grid->created_at(trans('admin.created_at'))->display(function (\$created_at) { \n";
            $grid[] = '   '." return Carbon::parse(\$created_at)->translatedFormat('d F Y H:m:s'); \n";
            $grid[] = "}); \n";
            $grid[] = "\$grid->updated_at(trans('admin.updated_at'))->display(function (\$updated_at) { \n";
            $grid[] = '   '." return Carbon::parse(\$updated_at)->translatedFormat('d F Y H:m:s'); \n";
            $grid[] = "}) \n;";
        }

        $this->listGrid = trim(implode(str_repeat(' ', 8), $grid), "\n");
        $this->listShow = trim(implode(str_repeat(' ', 8), $show), "\n");
        $this->listForm = trim(implode(str_repeat(' ', 8), $form), "\n");

        return $this;
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
        return __DIR__.'/stubs/controller.stub';
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
