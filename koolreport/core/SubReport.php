<?php

namespace koolreport\core;

trait SubReport
{
    public function __constructSubReport()
    {
        $this->registerEvent("OnInit",function()
        {
            $params = array_merge($this->params,$_POST);
            if(isset($params["@subReport"]))
            {
                $name = $params["@subReport"];
                unset($params["@subReport"]);
                $settings = $this->settings();
                $subReports = Utility::get($settings,"subReports");
                $class = Utility::get($subReports,$name);
                if($class!=null)
                {
                    $params["@reportName"] = $name;
                    $r = new $class($params);
                    $r->run()->render();
                }
                else
                {
                    header("HTTP/1.1 404 Could not find [$name] sub report");
                }
                exit;                
            }    
        });
        $this->registerEvent("OnResourceInit",function()
        {
            $this->getResourceManager()->addScriptFileOnBegin(
                $this->publishAssetFolder(realpath(dirname(__FILE__)."/../clients/jquery"))."/jquery.min.js"
            );
            $this->getResourceManager()->addScriptFileOnBegin(
                $this->publishAssetFolder(realpath(dirname(__FILE__)."/../clients/subreport"))."/subreport.js"
            );

        });
    }

    public function subReport($name,$params=array())
    {
		$settings = $this->settings();
		$subReports = Utility::get($settings,"subReports");
		$class = Utility::get($subReports,$name);
		if($class!=null)
		{
            $params["@reportName"] = $name;
            $r = new $class($params);
            echo "<sub-report id='$name' name='$name'>";
            $r->run()->render();
            echo "</sub-report>";
		}
		else
		{
			trigger_error("Could not find [$name] subreport, please define this report in the ".Utility::getClassName($this)."::settings()",E_USER_WARNING);
			return null;
		}
    }
}