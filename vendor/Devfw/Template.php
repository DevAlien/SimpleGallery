<?php
/**
 * Template engine which is the View part of the system
 *
 * With the template engine we can assign variables, objects, arrays etc.
 * The files are "compiled" in PHP and if possible, stored in a cache and the include them.
 *
 * @author Goncalo Margalho <gsky89@gmail.com>
 * @license LGPL(V3) http://www.opensource.org/licenses/lgpl-3.0.html
 * @version 1.0
 */
namespace Devfw;

/**
 * Template engine which is the View part of the system
 *
 * With the template engine we can assign variables, objects, arrays etc.
 * The files are "compiled" in PHP and if possible, stored in a cache and the include them.
 */
class Template {

    /**
     * Content of all Variables
     *
     * @var array
     */
    private $variables;

    /**
     * Directory of Template (skins)
     *
     * @var string
     */
    private $tpl_base = './app/views';

    /**
     * The directory of the templates directory
     *
     * @var string
     */
    private $tpl_dir;

    /**
     * Masterpage of template
     *
     * @var string
     */
    private $masterpage;

    /**
     * String with the content of file to write in the cache file
     *
     * @var String
     */
    private $compiledFile;

    /**
     * Array with the contents parts for insert to the masterpage
     *
     * @var array
     */
    private $fileContent = array();

    /**
     * name of page
     *
     * @var string
     */
    private $name;

    /**
     * Array with javascripts paths
     *
     * @var array
     */
    private $javascripts = array();

    /**
     * Array with css paths
     *
     * @var array
     */
    private $css = array();

    /**
     * Config class
     *
     * @var Config
     */
    private $config;

    /**
     * Set directory of Template and masterpage.
     *
     * @param string $template_dir Directory of Template
     * @param string $name if set will load the template indicated ()
     *
     * @return Void
     */
    public function __construct($templatedir = '', $name = false) {
        $this->setTemplateName($templatedir, $name);
    }

    public function setTemplateName($templatedir, $name = false) {
        if ($name == false){
            $this->name = $templatedir;
            $this->tpl_dir = $this->tpl_base . $this->name;
        }
        else{
            $this->tpl_dir = $templatedir;
            $this->name = $name;
        }
        $this->masterpage = $this->tpl_dir . '/master.page';
    }

    /**
     * Compile the masterpage with the content of the tpl page and add the title, js and css etc.
     *
     * @return Void
     */
    private function compileMasterpage() {
        $this->compiledFile = file_get_contents($this->masterpage);
        $this->compileTPL($this->tpl_dir, 'master.page');
        $this->compiledFile = preg_replace_callback('/{jsandcss(?:\s+)js="(.*?)"(?:\s+)css="(.*?)"}/', array( &$this, 'getJSAndCSS'), $this->compiledFile);
        $this->compiledFile = preg_replace_callback('/(?:{content(?:\s+)name="(.*?)"}([\S|\s]*?){\/content})/', array(&$this, 'loadContent'), $this->compiledFile);
    }

    /**
     * Assign variable to a template
     *
     * @param string $variable_name Name of variable in template
     * @param mixed $value Value of variable assigned
     *
     * @return Void
     */
    public function assign($variable_name, $value) {
        $this->variables[$variable_name] = $value;
    }

    /**
     * Delete old compiled file and compile a new file
     *
     * @param string $template_dir Directory of template
     * @param string $tpl_name Name of template
     *
     * @return Void
     */
    private function compileTPL($template_dir, $tpl_name = false) {
        if (is_writable($this->getCache()))
            if ($file = glob($this->getCache() . $tpl_name . '*.php'))
                foreach ($file as $delfile)
                    unlink($delfile);

        if($tpl_name != false)
            $tpl = file_get_contents($template_dir . '/' . $tpl_name);
        else
            $tpl = file_get_contents($template_dir);

        $compiling = preg_replace('/{\$\$(.[^}]*?)\.(.*?)}/', '<?php echo $\\1->get\\2();?>', $tpl);
        $compiling = preg_replace('/{\$\$(.*?)}/', '<?php echo $\\1;?>', $compiling);
        $compiling = preg_replace('/{\$(.[^}]*?)\.(.*?)}/', '<?php echo $\\1[\'\\2\'];?>', $compiling);
        $compiling = preg_replace('/{\_(.[^}]*?)\.(.*?)}/', '<?php echo $var[\'\\1\'][\'\\2\'];?>', $compiling);
        $compiling = preg_replace('/{config\.(.*?)}/', '<?php echo $this->config->get(\'\\1\');?>', $compiling);
        $compiling = preg_replace('/\[\$\$(.[^\]]*?)\.(.*?)\]/', '$\\1->get\\2()',$compiling);
        $compiling = preg_replace('/\[\$(.[^\]]*?)\.(.*?)\]/', '$\\1[\'\\2\']',$compiling);
        $compiling = preg_replace('/{\$key\.(.*?)}/', '<?php echo $key[\'\\1\'];?>', $compiling);
        $compiling = preg_replace('/{date\.\$(.*?)\.(.*?)}/', '<?php echo date(\'d-m-Y H:i\',$\\1[\'\\2\']);?>', $compiling);
        $compiling = preg_replace('/{date\.time}/', '<?php echo date(\'d-m-Y H:i\',time());?>', $compiling);
        $compiling = preg_replace('/{date\.(.*?)}/', '<?php echo date(\'d-m-Y H:i\',$var[\'\\1\']);?>', $compiling);
        $compiling = preg_replace('/\[\$key\.(.*?)\]/', '$key[\'\\1\']', $compiling);
        $compiling = preg_replace('/\[\$value\]/', '$value', $compiling);
        $compiling = str_replace('{$value}', '<?php echo $value;?>', $compiling);
        $compiling = str_replace('{$key}', '<?php echo $key;?>', $compiling);
        $compiling = preg_replace('/{\$(.*?)}/', '<?php echo $var[\'\\1\'];?>', $compiling);
        $compiling = preg_replace('/\[\$(.*?)\]/', '$var[\'\\1\']', $compiling);
        $compiling = preg_replace('/{(.*?)\:\:(.*?)}/', '<?php echo \\1::\\2;?>', $compiling);
        $compiling = preg_replace('/\[counter.(.*?)\]/', '$counter_\\1', $compiling);
        $compiling = preg_replace('/(?:{if(?:\s+)condition="(.*?)"})/', '<?php if(\\1){ ?>', $compiling);
        $compiling = preg_replace('/(?:{elseif(?:\s+)condition="(.*?)"})/', '<?php } else if(\\1){ ?>', $compiling);
        $compiling = str_replace('{else}', '<?php } else{ ?>', $compiling);
        $compiling = str_replace('{/if}', '<?php } ?>', $compiling);
        $compiling = preg_replace('/(?:{loop(?:\s+)name="(.*?)"})/', '<?php $counter_\\1=0; foreach($var[\'\\1\'] as $key => $\\1){ $counter_\\1++; ?>', $compiling);
        $compiling = str_replace('{/loop}', '<?php } ?>', $compiling);
        if ($tpl_name != 'master.page')
            $compiling = preg_replace_callback('/(?:{content(?:\s+)name="(.*?)"}([\S|\s]*?){\/content})/', array(&$this, 'setFileContent'), $compiling);

        $this->compiledFile = $compiling;
    }

    /**
     * Compile template, HTML to PHP
     *
     * @param string $tpl_name Name of file to compile
     * @param string $ext Extension of file to compile
     * @param boolean $withMasterpage if you can use the masterpage. Default is true
     * @param boolean $echo if you can cache the file and after include it or print the compiled template
     * @return void
     */
    public function burn($tpl_name, $ext, $withMasterpage = true, $echo = false) {
        $default = 0;
        $var = $this->variables;

        if(strpos($tpl_name, '/') !== false){
            $template = $tpl_name . '.' . $ext;
            $tplname = str_replace('/', '', $template);
        }  
        else{
            $default = 1;
            $template = $this->tpl_dir . '/' . $tpl_name . '.' . $ext;
            $tplname = $tpl_name . '.' . $ext;
        }

        if (!file_exists($template)) {
            echo 'The system tried to use the file: ' . $template . ' but doesn\'t exists<br /><br />Return to the <a href="' . $this->config->get('base_url') . '">site</a>';
            exit();
        }

        $tpltime = filemtime($template);
        if (file_exists($this->masterpage))
            $mastertime = filemtime($this->masterpage);
        else
            $mastertime = 0;
        if ($echo == false) {
            if (file_exists($this->getCache() . $tplname . '_' . $tpltime . '_' . $mastertime . '.php'))
                include $this->getCache() . $tplname . '_' . $tpltime . '_' . $mastertime . '.php';
            else {
                if($default == 0)
                    $this->compileTPL($template);
                else
                    $this->compileTPL($this->tpl_dir, $tpl_name . '.' . $ext);
                if($withMasterpage == true) {
                    $this->compileMasterpage();
                }
                if (is_writable($this->getCache())) {
                    fwrite(fopen($this->getCache(). $tplname . '_' . $tpltime . '_' . $mastertime . '.php', 'w'), $this->compiledFile);
                    include $this->getCache() . $tplname . '_' . $tpltime . '_' . $mastertime . '.php';
                }
                else
                    eval('?>' . $this->compiledFile);
            }
        }
        else
            echo $this->compiledFile;
    }

    /**
     * Set the content of the "contents" in the fileContent array
     *
     * @param Array $content
     * @return String
     */
    private function setFileContent($content) {
        $this->fileContent[$content[1]] = $content[2];
        return $content[2];
    }

    /**
     * Load the contents
     *
     * @param array $matches
     * @return String
     */
    private function loadContent($matches) {
        if (array_key_exists($matches[1], $this->fileContent))
            return $this->fileContent[$matches[1]];

        return $matches[2];
    }
    
    /**
     * Get js and css strings
     *
     * @param array $jsi All the found array from the preg_replace_callback
     * @return String
     */
    private function getJSAndCSS($jsi) {
        $html = '';
        $cssws = '';
        $jsws = '';
        foreach($this->css as $css)
            $cssws .= $css.'|';
        if(strlen($jsi[2]) == 0)
            $cssws = rtrim($cssws, '|');
        $html .= '<link rel="stylesheet" type="text/css" href="'.$this->config->get('base_url') .'system/pages/bootstrap.css.php?'.$cssws.$jsi[2].'" media="screen"/> ';
        foreach($this->javascripts as $js)
            $jsws .= $js.'|';
        if(strlen($jsi[1]) == 0)
            $jsws = rtrim($jsws, '|');
        $html .= '<script type="text/javascript" src="'.$this->config->get('base_url') .'system/pages/bootstrap.js.php?'.$jsws.$jsi[1].'"></script>';

        return $html;
    }

    /**
     * Get the cache directory and if not exists try to make it
     *
     * @return String Path of the cache
     */
    private function getCache(){
        $cache = 'cache/views/' . $this->name . '/';
        
        return $cache;
    }

    /**
     * Set the config
     *
     * @param Config $config The Config object
     *
     * @return void
     */
    public function setConfig(\Devfw\Config $config){
        $this->config = $config;
    }

}