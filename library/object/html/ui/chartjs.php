<?php
/*
    Name:           HTML UI Chart JS
    Purpose:        User object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 12, 2018 10:51 AM
*/

namespace sPHP\HTML\UI;

class ChartJS{
    #region Property variable
    private $Property = [
		"Recordset"					=>	[],
		"Dataset"					=>	[],
		"Label"						=>	null,
		"XAxes"						=>	null,
		"YAxes"						=>	null,
		"ID"						=>	null,
		"Type"						=>	\sPHP\CHART_TYPE_LINE,
		"Title"						=>	null,
		"TitleFontColor"			=>	"Black",
		"TitleFontSize"				=>	14,
		"TitleFontStyle"			=>	"Bold",
		"LegendFontColor"			=>	"Black",
		"LegendFontSize"			=>	12,
		"LegendFontStyle"			=>	"Bold",
		"AspectRatio"				=>	2,
		"MaintainAspectRatio"		=>	false,
		"Responsive"				=>	true,
        "HTML"						=>	null,
    ];
    #endregion Property variable

    #region Method
    public function __construct($Recordset = null, $Dataset = null, $Label = null, $XAxes = null, $YAxes = null, $ID = null, $Type = null, $Title = null, $TitleFontColor = null, $TitleFontSize = null, $TitleFontStyle = null, $LegendFontColor = null, $LegendFontSize = null, $LegendFontStyle = null, $AspectRatio = null, $MaintainAspectRatio = null, $Responsive = null){
		// Set default automatic values
		$this->Property["ID"] = "ChartJS_" . str_replace(".", "_", microtime(true)) . "_" . \sPHP\GUID() . "";

        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }
    #endregion Method

    #region Property
    public function Recordset($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			if(is_null($this->Property["Label"]))$this->Property["Label"] = array_keys($this->Property["Recordset"][array_keys($this->Property["Recordset"])[0]])[0];
			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function Dataset($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function Label($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function XAxes($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function YAxes($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function ID($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function Type($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function Title($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function TitleFontColor($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function TitleFontSize($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function TitleFontStyle($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function LegendFontColor($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function LegendFontSize($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function LegendFontStyle($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function AspectRatio($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function MaintainAspectRatio($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function Responsive($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function HTML(){
		if(is_null($this->Property[__FUNCTION__])){
//var_dump($this->Property["Dataset"]); exit;
			foreach($this->Property["Dataset"] as $Dataset){
				$Dataset->Data(array_column($this->Property["Recordset"], $Dataset->Name()));
				$DatasetJavaScript[] = $Dataset->JavaScript();
			}

			foreach(array_column($this->Property["Recordset"], $this->Property["Label"]) as $Label)$LabelJavaScript[] = "'" . str_replace("'", "\\'", $Label) . "'";

			$this->Property[__FUNCTION__] = "
				<div id=\"{$this->Property["ID"]}_Container\" class=\"{$this->Property["ID"]}_Container\"><canvas id=\"{$this->Property["ID"]}\"></canvas></div>

				<script>
					{$this->Property["ID"]} = new Chart(document.getElementById('{$this->Property["ID"]}'), {
						type: '" . strtolower($this->Property["Type"]) . "',
						data: {
							labels: [" . implode(", ", $LabelJavaScript) . "],
							datasets: [" . implode(", ", $DatasetJavaScript) . "],
						},
						options: {
							responsive: " . ($this->Property["Responsive"] ? "true" : "false") . ",
							aspectRatio: {$this->Property["AspectRatio"]},
							maintainAspectRatio: " . ($this->Property["MaintainAspectRatio"] ? "true" : "false") . ",
							" . ($this->Property["Title"] ? "title: {
								display: true,
								text: '" . str_replace("'", "\\'", $this->Property["Title"]) . "',
								fontColor: '{$this->Property["TitleFontColor"]}',
								fontSize: '{$this->Property["TitleFontSize"]}',
								fontStyle: '" . strtolower($this->Property["TitleFontStyle"]) . "',
							}, " : null) . "
							tooltips: {
								mode: 'index',
								intersect: false,
							},
							hover: {
								mode: 'nearest',
								intersect: true,
							},
							" . (!is_null($this->Property["XAxes"]) && !is_null($this->Property["YAxes"]) ? "scales: {
								" . (!is_null($this->Property["XAxes"]) ? "xAxes: {$this->Property["XAxes"]->ScaleJavaScript()}, " : null) . "
								" . (!is_null($this->Property["YAxes"]) ? "yAxes: {$this->Property["YAxes"]->ScaleJavaScript()}, " : null) . "
							}, " : null) . "
							legend: {
								display: true,
								labels: {
									fontColor: '{$this->Property["LegendFontColor"]}',
									//fontSize: '{$this->Property["LegendFontSize"]}',
									fontStyle: '" . strtolower($this->Property["LegendFontStyle"]) . "',
								},
							},
						},
					});
				</script>
			";
		}

		$Result = $this->Property[__FUNCTION__];

		return $Result;
    }

    public function ToggleDataset($DatasetIndex, $ScriptTag = true){
		if(!is_array($DatasetIndex))$DatasetIndex = [$DatasetIndex];

		$JavsScript = [];
		foreach($DatasetIndex as $ThisDatasetIndex)$JavsScript[] = "{$this->Property["ID"]}.getDatasetMeta({$ThisDatasetIndex}).hidden = !{$this->Property["ID"]}.getDatasetMeta({$ThisDatasetIndex}).hidden;";
		if(count($DatasetIndex))$JavsScript[] = "{$this->Property["ID"]}.update();";

		$Result = ($ScriptTag ? "<script>" : null) . implode("\n", $JavsScript) . ($ScriptTag ? "</script>" : null);

		return $Result;
    }
    #endregion Property
}
?>