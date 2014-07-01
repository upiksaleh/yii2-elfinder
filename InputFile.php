<?php
/**
 * Date: 23.01.14
 * Time: 1:27
 */

namespace mihaildev\elfinder;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;



class InputFile extends InputWidget{

    public $language;

    public $filter;

    public $buttonName = 'Browse';
    public $buttonOptions = [];

    protected $_managerOptions;

    public $width = 'auto';
    public $height = 'auto';

    public $template = '{input}{button}';

    public $controller = 'elfinder';

    public $multiple;

    public function init()
    {
        parent::init();

        if(empty($this->language))
            $this->language = ElFinder::getSupportedLanguage(Yii::$app->language);

        if(empty($this->buttonOptions['id']))
            $this->buttonOptions['id'] = $this->options['id'].'_button';

        $this->buttonOptions['type'] = 'button';

        $managerOptions = [];
        if(!empty($this->filter))
            $managerOptions['filter'] = $this->filter;

        $managerOptions['callback'] = $this->options['id'];

        $managerOptions['lang'] = $this->language;

        if (!empty($this->multiple))
            $managerOptions['multiple'] = $this->multiple;

        $this->_managerOptions['url'] = ElFinder::getManagerUrl($this->controller, $managerOptions);
        $this->_managerOptions['width'] = $this->width;
        $this->_managerOptions['height'] = $this->height;
        $this->_managerOptions['id'] = $this->options['id'];
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        if ($this->hasModel()) {
            $replace['{input}'] = Html::activeTextInput($this->model, $this->attribute, $this->options);
        } else {
            $replace['{input}'] = Html::textInput($this->name, $this->value, $this->options);
        }

        $replace['{button}'] = Html::button($this->buttonName, $this->buttonOptions);


        echo strtr($this->template, $replace);

        AssetsCallBack::register($this->getView());

        if (!empty($this->multiple))
            $this->getView()->registerJs("ElFinderFileCallback.register(".Json::encode($this->options['id']).", function(files, id){ var _f = []; for (var i in files) { _f.push(files[i].url); } \$('#' + id).val(_f.join(', ')); return true;}); $('#".$this->buttonOptions['id']."').click(function(){ElFinderFileCallback.openManager(".Json::encode($this->_managerOptions).");});");
        else
            $this->getView()->registerJs("ElFinderFileCallback.register(".Json::encode($this->options['id']).", function(file, id){ \$('#' + id).val(file.url); return true;}); $('#".$this->buttonOptions['id']."').click(function(){ElFinderFileCallback.openManager(".Json::encode($this->_managerOptions).");});");
    }
}
