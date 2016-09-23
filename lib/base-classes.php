<?php

// Links, Nodes and the Map object inherit from this class ultimately.
// Just to make some common code common.
class WeatherMapBase
{
    public $name;

    var $notes = array();
    var $hints = array();
    var $inherit_fieldlist;
    var $imap_areas = array();

    protected $config = array();
    protected $descendents = array();
    protected $dependencies = array();

    function __construct()
    {
        $this->config = array();
        $this->descendents = array();
        $this->dependencies = array();
    }

    function __toString()
    {
        return $this->my_type() . " " . (isset($this->name) ? $this->name : "[unnamed]");
    }

    public function my_type()
    {
        return "BASE";
    }

    function add_note($name, $value)
    {
        wm_debug("Adding note $name='$value' to " . $this->name . "\n");
        $this->notes[$name] = $value;
    }

    function get_note($name)
    {
        if (isset($this->notes[$name])) {
            //	debug("Found note $name in ".$this->name." with value of ".$this->notes[$name].".\n");
            return ($this->notes[$name]);
        } else {
            //	debug("Looked for note $name in ".$this->name." which doesn't exist.\n");
            return (NULL);
        }
    }

    function add_hint($name, $value)
    {
        wm_debug("Adding hint $name='$value' to " . $this->name . "\n");
        $this->hints[$name] = $value;
        # warn("Adding hint $name to ".$this->my_type()."/".$this->name."\n");
    }


    function get_hint($name)
    {
        if (isset($this->hints[$name])) {
            //	debug("Found hint $name in ".$this->name." with value of ".$this->hints[$name].".\n");
            return ($this->hints[$name]);
        } else {
            //	debug("Looked for hint $name in ".$this->name." which doesn't exist.\n");
            return (NULL);
        }
    }


    /**
     * Get a value for a config variable. Follow the template inheritance tree if necessary.
     * Return an array with the value followed by the status (whether it came from the source object or
     * a template, or just didn't exist). This will replace all that CopyFrom stuff.
     *
     * @param $keyname
     * @return array
     */
    public function getConfigValue($keyname)
    {
        if (isset($this->config[$keyname])) {
            return array($this->config[$keyname], CONF_FOUND_DIRECT);
        } else {
            if (!is_null($this->parent)) {
                list($value, $direct) = $this->parent->getConfig($keyname);
                if ($direct != CONF_NOT_FOUND) {
                    $direct = CONF_FOUND_INHERITED;
                }
            } else {
                $value = null;
                $direct = CONF_NOT_FOUND;
            }

            // if we got to the top of the tree without finding it, that's probably a typo in the original getConfig()
            if (is_null($value) && is_null($this->parent)) {
                wm_warn("Tried to get config keyword '$keyname' with no result. [WMWARN300]");
            }
            return array($value, $direct);
        }
    }

    public function getConfigValueWithoutInheritance($keyname)
    {
        if (isset($this->config[$keyname])) {
            return $this->config[$keyname];
        }
        return array(null);
    }

    /*
     * Set a new value for a config variable. If $recalculate is true (after the initial readConfig)
     * then also recursively tell all objects that have us as a template that their state has changed
     *
     * return an array of the objects that were notified
     */
    public function setConfigValue($keyname, $value, $recalculate = false)
    {
        wm_debug("Settings config %s = %s\n", $keyname, $value);
        if (is_null($value)) {
            unset($this->config[$keyname]);
        } else {
            $this->config[$keyname] = $value;
        }

        if ($recalculate) {
            $affected = $this->recalculate();
            return $affected;
        }
        return array($this->name);
    }

    public function addConfigValue($keyname, $value, $recalculate = false)
    {
        wm_debug("Appending config %s = %s\n", $keyname, $value);
        if (is_null($this->config[$keyname])) {
            // create a new array, with this as the only item
            $this->config[$keyname] = array($value);
        } else {
            if (is_array($this->config[$keyname])) {
                // append the new item to the existing array
                $this->config[$keyname] [] = $value;
            } else {
                // This is the second value, so make a new array of the old one, and this one
                $this->config[$keyname] = array($this->config[$keyname], $value);
            }
        }

        if ($recalculate) {
            $affected = $this->recalculate();
            return $affected;
        }
        return array($this->name);
    }

    public function setTemplate($template_name, $owner)
    {
        $this->template = $template_name;
        wm_debug("Resetting to template %s %s\n", $this->my_type(), $template_name);
        $this->reset($owner);
    }

}

class WeatherMapConfigItem
{
    var $defined_in;
    var $name;
    var $value;
    var $type;
}

// The 'things on the map' class. More common code (mainly variables, actually)
class WeatherMapItem extends WeatherMapBase
{
    var $owner;

    var $configline;
    var $infourl;
    var $overliburl;
    var $overlibwidth, $overlibheight;
    var $overlibcaption;
    var $my_default;
    var $defined_in;
    var $config_override;    # used by the editor to allow text-editing

    function my_type()
    {
        return "ITEM";
    }

    public function getZIndex()
    {
        return $this->zorder;
    }
}
