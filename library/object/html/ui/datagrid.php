<?php
/*
    Name:           HTML UI Datagrid
    Purpose:        User object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 12, 2018 10:51 AM
*/

namespace sPHP\HTML\UI;

class Datagrid{
    #region Property variable
    private $Property = [
        "Data"						=>	[],
		"URL"						=>	null,
		"RecordCount"				=>	0,
        "Field"						=>	[],
		"Title"						=>	null,
		"RowPerPage"				=>	50,
		"DataIDColumn"				=>	null,
		"Action"					=>	[],
		"BaseURL"					=>	"./",
		"IconBaseURL"				=>	"./image/icon/",
		"Footer"					=>	null,
		"PreHTML"					=>	null,
		"BatchAction"				=>	[],
		"ExpandURL"					=>	null,
		"Serial"					=>	true,
		"Selectable"				=>	true,
        "ID"						=>	null,
        "CSSSelector"				=>	null,
        "SerialCaption"				=>	"#",
        "PaginatorPageCaption"      =>  "Page", 
        "PaginatorRecordsCaption"   =>  "Record(s)", 

        // Read only
        "HTML"						=>	null,
    ];
    #endregion Property variable

    #region Method
    public function __construct($Data = null, $URL = null, $RecordCount = null, $Field = null, $Title = null, $RowPerPage = null, $DataIDColumn = null, $Action = null, $BaseURL = null, $IconBaseURL = null, $Footer = null, $PreHTML = null, $BatchAction = null, $ExpandURL = null, $Serial = null, $Selectable = null, $ID = null, $CSSSelector = null, $SerialCaption = null, $PaginatorPageCaption = null, $PaginatorRecordsCaption = null){ //\sPHP\DebugDump(get_defined_vars());
		// Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }
    #endregion Method

    #region Property
    public function Data($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = is_array($Value) ? $Value : [];

			$this->Property["HTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function URL($Value = null){
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

    public function RecordCount($Value = null){
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

    public function Field($Value = null){
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

    public function RowPerPage($Value = null){
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

    public function DataIDColumn($Value = null){
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

    public function Action($Value = null){
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

    public function BaseURL($Value = null){
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

    public function IconBaseURL($Value = null){
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

    public function Footer($Value = null){
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

    public function PreHTML($Value = null){
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

    public function BatchAction($Value = null){
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

    public function ExpandURL($Value = null){
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

    public function Serial($Value = null){
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

    public function Selectable($Value = null){
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

    public function CSSSelector($Value = null){
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

    public function SerialCaption($Value = null){
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

    public function PaginatorPageCaption($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{ //\sPHP\DebugDump($this->Property[__FUNCTION__]);
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function PaginatorRecordsCaption($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{ //\sPHP\DebugDump($this->Property[__FUNCTION__]);
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function HTML(){
		if(is_null($this->Property[__FUNCTION__])){
            //\sPHP\DebugDump($this->Property);
            // CHECK BACK: Can't we just use array_filter() below?
			$Field = array_values(array_filter($this->Property["Field"]));

            $URL = "{$this->Property["URL"]}" . (strpos($this->Property["URL"], "?") === false ? "?" : "&") . "";
			$ParameterPrefix = $this->Property["ID"] ? "Datagrid_{$this->Property["ID"]}_" : null;

			\sPHP\SetVariable("{$ParameterPrefix}Page", 1);
			\sPHP\SetVariable("{$ParameterPrefix}OrderBy", $Field[0]->Name());
			\sPHP\SetVariable("{$ParameterPrefix}Order", "ASC");

			#region Column title
			if($this->Property["Serial"])$ColumnTitleHTML[] = "<th class=\"Serial\">" . ($this->Property["Selectable"] ? "<label>{$this->Property["SerialCaption"]}<input id=\"{$ParameterPrefix}SelectToggle\" type=\"checkbox\" onclick=\"sJS.ToggleCheckBoxes('{$this->Property["DataIDColumn"]}', this.checked, null);\"></label>" : "{$this->Property["SerialCaption"]}") . "</th>";

			if($this->Property["ExpandURL"])$ColumnTitleHTML[] = "<th class=\"Expansion\"></th>";

			foreach($Field as $Column){
				$CaptionHTML = is_null($Column->Caption()) ? $Column->Name() : $Column->Caption();

				if($CaptionHTML && $Column->Sortable()){
					$NextOrderDirection = $_POST["{$ParameterPrefix}OrderBy"] == $Column->Name() ? ($_POST["{$ParameterPrefix}Order"] == "ASC" ? "DESC" : "ASC") : "ASC";
					$CaptionHTML = "<a href=\"{$URL}{$ParameterPrefix}OrderBy={$Column->Name()}&{$ParameterPrefix}Order={$NextOrderDirection}\" class=\"Title\">{$CaptionHTML}" . ($Column->Name() == $_POST["{$ParameterPrefix}OrderBy"] ? "<span class=\"OrderDirection" . ($NextOrderDirection == "ASC" ? "Des" : "As") . "cending\"></span>" : null) . "</a>";
				}

				$ColumnTitleHTML[] = "<th>{$CaptionHTML}</th>";
			}

			if(count($this->Property["Action"]))$ColumnTitleHTML[] = "<th></th>";
			#endregion Column title

            $SerialStart = (($_POST["{$ParameterPrefix}Page"] - 1) * $this->Property["RowPerPage"]) + 1;
            
            $DataKeyFieldForTemplate = [];
            if(count($this->Property["Data"]))foreach(array_keys($this->Property["Data"][0]) as $DataKey)$DataKeyFieldForTemplate[] = "%{$DataKey}%";

			foreach($this->Property["Data"] as $DataIndex => $Data){ // Generate rows from data
				$FieldHTML = $ActionHTML = [];

				if($this->Property["Serial"]){
					$Serial = $SerialStart + $DataIndex;

					if($this->Property["Selectable"]){
						$FieldHTML[] = "<td class=\"Serial\"><label>{$Serial}<input id=\"{$ParameterPrefix}{$this->Property["DataIDColumn"]}[{$Data[$this->Property["DataIDColumn"]]}]\" type=\"checkbox\" name=\"{$this->Property["DataIDColumn"]}[{$Data[$this->Property["DataIDColumn"]]}]\" value=\"{$Data[$this->Property["DataIDColumn"]]}\"></label></td>";
					}
					else{
						$FieldHTML[] = "<td class=\"Serial\">{$Serial}</td>";
					}
				}

				if($this->Property["ExpandURL"]){
					$ExpansionAreaHTMLID = "{$ParameterPrefix}ExpansionArea_" . \sPHP\GUID() . "";
					$FieldHTML[] = "<td class=\"Expander\" onclick=\"sJS.ToggleVisibilityByElemntID('{$ExpansionAreaHTMLID}'); document.getElementById('{$ExpansionAreaHTMLID}_Cell').innerHTML = '<iframe src=\\'{$this->Property["ExpandURL"]}" . (strpos($this->Property["ExpandURL"], "?") === false ? "?" : "&") . "{$this->Property["DataIDColumn"]}={$Data[$this->Property["DataIDColumn"]]}\\'></iframe>';\"></td>";
				}

				foreach($Field as $Column){
					$ColumnCSSSelector = [];

                    #region Format display data
                    $ColumnData = $Column->Data($Data[$Column->Name()]); // Get the data as formatted by Column object

                    // Transform data using Template
                    if(strlen($ColumnData) && $Column->Template() && count($DataKeyFieldForTemplate))$ColumnData = str_replace($DataKeyFieldForTemplate, $Data, $Column->Template());

					if($Column->Type() == \sPHP\FIELD_TYPE_EMAIL){
						if($ColumnData)$ColumnData = "<a href=\"mailto:{$ColumnData}\">{$ColumnData}</a>";
					}
					elseif($Column->Type() == \sPHP\FIELD_TYPE_PHONE){
						if($ColumnData)$ColumnData = "<a href=\"tel:{$ColumnData}\">{$ColumnData}</a>";
					}
					elseif($Column->Type() == \sPHP\FIELD_TYPE_BOOLEANICON && strlen($ColumnData)){
						$DataCaption = $ColumnData == 0 ? "False" : ($ColumnData == 1 ? "True" : null);
						$IconPrefix = $Column->IconPrefix() ? $Column->IconPrefix() : "bubble";
						$ColumnData = "<img src=\"{$this->Property["IconBaseURL"]}{$IconPrefix}_" . strtolower($DataCaption) . ".png\" alt=\"{$DataCaption}\" title=\"{$Column->Caption()}: {$DataCaption}\" class=\"Icon\">";
						if(!$Column->Align())$Column->Align(\sPHP\ALIGN_CENTER);
					}
					elseif($Column->Type() == \sPHP\FIELD_TYPE_ICON){
						if($ColumnData){
							$DataCaption = basename($ColumnData);
							$PathInfo = pathinfo(strtolower($ColumnData));
							$ColumnData = "<img src=\"{$this->Property["IconBaseURL"]}{$Column->IconPrefix()}" . str_replace(" ", "_", $PathInfo["filename"]) . "." . (isset($PathInfo["extension"]) ? $PathInfo["extension"] : "png") . "\" alt=\"{$DataCaption}\" title=\"{$Column->Caption()}: {$DataCaption}\" class=\"Icon\">";
							if(!$Column->Align())$Column->Align(\sPHP\ALIGN_CENTER);
						}
					}
					elseif($Column->Type() == \sPHP\FIELD_TYPE_ICONURL){
						if($ColumnData){
							$DataCaption = basename($ColumnData);
							$ColumnData = "<a href=\"{$this->Property["IconBaseURL"]}{$ColumnData}\" target=\"_blank\"><img src=\"{$this->Property["BaseURL"]}{$ColumnData}\" alt=\"{$DataCaption}\" class=\"Icon\"></a>";
							if(!$Column->Align())$Column->Align(\sPHP\ALIGN_CENTER);
						}
					}
					elseif($Column->Type() == \sPHP\FIELD_TYPE_PICTURE){
						if($ColumnData){
							$DataCaption = basename($ColumnData);
							$ColumnData = "<img src=\"{$this->Property["BaseURL"]}{$ColumnData}\" alt=\"{$DataCaption}\" class=\"Picture\">";
							if(!$Column->Align())$Column->Align(\sPHP\ALIGN_CENTER);
						}
					}
					elseif($Column->Type() == \sPHP\FIELD_TYPE_PICTUREURL){
						if($ColumnData){
							$DataCaption = basename($ColumnData);
							$ColumnData = "<a href=\"{$this->Property["BaseURL"]}{$ColumnData}\" target=\"_blank\"><img src=\"{$this->Property["BaseURL"]}{$ColumnData}\" alt=\"{$DataCaption}\" class=\"Picture\"></a>";
							if(!$Column->Align())$Column->Align(\sPHP\ALIGN_CENTER);
						}
					}
					elseif($Column->Type() == \sPHP\FIELD_TYPE_URL){
						if($ColumnData){
							$ColumnData = "<a href=\"{$ColumnData}\">{$ColumnData}</a>";
							if(!$Column->Align())$Column->Align(\sPHP\ALIGN_CENTER);
						}
					}
					elseif($Column->Type() == \sPHP\FIELD_TYPE_URLICON){
						if($ColumnData){
							$ColumnData = "<a href=\"{$ColumnData}\"" . ($Column->Target() ? " target=\"{$Column->Target()}\"" : null) . "><img src=\"{$this->Property["IconBaseURL"]}" . strtolower($Column->Icon()) . ".png\" alt=\"{$Column->Icon()}\" class=\"Icon\"></a>";
							if(!$Column->Align())$Column->Align(\sPHP\ALIGN_CENTER);
						}
					}
					elseif($Column->Type() == \sPHP\FIELD_TYPE_GOOGLEMAPS){
						if($ColumnData){
							$ColumnData = "<a href=\"{$ColumnData}\" target=\"_blank\"><img src=\"{$this->Property["IconBaseURL"]}googlemaps.png\" alt=\"Google Maps\" class=\"Icon\"></a>";
							if(!$Column->Align())$Column->Align(\sPHP\ALIGN_CENTER);
						}
					}
					elseif($Column->Type() == \sPHP\FIELD_TYPE_NUMBER){
						if(!$Column->Align())$Column->Align(\sPHP\ALIGN_RIGHT);
					}
					elseif($Column->Type() == \sPHP\FIELD_TYPE_INTEGER){
                        if($ColumnData)$ColumnData = intval($ColumnData);
                        if(!$Column->Align())$Column->Align(\sPHP\ALIGN_RIGHT);
					}
					else{

                    }
					#endregion Format display data

					#region Set explicit alignment
					if($Column->Align() == \sPHP\ALIGN_LEFT){
						$ColumnCSSSelector[] = "AlignLeft"; // Left
					}
					elseif($Column->Align() == \sPHP\ALIGN_CENTER){
						$ColumnCSSSelector[] = "AlignCenter"; // Center
					}
					elseif($Column->Align() == \sPHP\ALIGN_RIGHT){
						$ColumnCSSSelector[] = "AlignRight"; // Right
					}
					else{

					}
					#endregion Set explicit alignment

					$FieldHTML[] = "<td" . (count($ColumnCSSSelector) ? " class=\"" . implode(" ", $ColumnCSSSelector) . "\"" : null) . "><span class=\"FieldCaption\">{$Column->Caption()}</span>" . ($ColumnData ? $Column->Prefix() : null) . "{$ColumnData}" . ($ColumnData ? $Column->Suffix() : null) . "</td>";
				}

				foreach(array_filter($this->Property["Action"]) as $Action){
					if(!$Action->Name() && !$Action->URL())$Action->URL($URL);

					if($Action->URL()){
						$OriginalActionURL = $Action->URL();
						$Action->URL("{$Action->URL()}" . (strpos($Action->URL(), "?") === false ? "?" : "&") . "" . (is_null($Action->ParameterKey()) ? "{$this->Property["DataIDColumn"]}" : "{$Action->ParameterKey()}") . "={$Data[$this->Property["DataIDColumn"]]}" . ($Action->SelfTarget() ? "&{$ParameterPrefix}Page={$_POST["{$ParameterPrefix}Page"]}&{$ParameterPrefix}OrderBy=" . urlencode($_POST["{$ParameterPrefix}OrderBy"]) . "&{$ParameterPrefix}Order={$_POST["{$ParameterPrefix}Order"]}" : null) . "");
						$ActionHTML[] = "{$Action->HTML()}";
						$Action->URL($OriginalActionURL);
					}
					else{
						$ActionHTML[] = "{$Action->HTML()}";
					}
				}

				$DataHTML[] = "<tr>" . implode(null, $FieldHTML) . (count($ActionHTML) ? "<td class = \"Action\">" . implode(null, $ActionHTML) . "</td>" : null) . "</tr>";
				if($this->Property["ExpandURL"])$DataHTML[] = "<tr id=\"{$ExpansionAreaHTMLID}\" class=\"Expansion\" style=\"display: none;\"><td id=\"{$ExpansionAreaHTMLID}_Cell\" colspan=\"99999\">EXPANSION AREA</td></tr>";
			}

			foreach($this->Property["BatchAction"] as $BatchAction)$BatchActionHTML[] = $BatchAction->HTML();

			$Paginator = new Paginator($this->Property["RecordCount"], $this->Property["RowPerPage"], null, "{$URL}{$ParameterPrefix}OrderBy={$_POST["{$ParameterPrefix}OrderBy"]}&{$ParameterPrefix}Order={$_POST["{$ParameterPrefix}Order"]}", null, $this->Property["ID"]);

			$Form = new Form(
				"{$URL}{$ParameterPrefix}Page={$_POST["{$ParameterPrefix}Page"]}&{$ParameterPrefix}OrderBy={$_POST["{$ParameterPrefix}OrderBy"]}&{$ParameterPrefix}Order={$_POST["{$ParameterPrefix}Order"]}",
				"
					<table id=\"DatagridTable_{$this->Property["ID"]}\" class=\"Grid" . ($this->Property["CSSSelector"] ? " {$this->Property["CSSSelector"]}" : null) . "\">
						<thead>
							<tr class=\"Title\">
								<th colspan=\"99999\">
									" . ($this->Property["Title"] ? "<div class=\"Caption\">{$this->Property["Title"]}</div>" : null) . "
									" . (isset($BatchActionHTML) ? "<div class=\"Action\">" . implode(" ", $BatchActionHTML) . "</div>" : null) . "
								</th>
							</tr>

							" . ($this->Property["PreHTML"] ? "<tr class=\"PreHTML\"><th colspan=\"99\">{$this->Property["PreHTML"]}</th></tr>" : null) . "

							<tr class=\"Page\">
								<th colspan=\"99999\">
									<div class=\"Paginator\">{$Paginator->HTML()}</div>
									<div class=\"Suffix\">{$this->Property["PaginatorPageCaption"]} {$Paginator->CurrentPage()} / {$Paginator->PageCount()}: " . ($RecordFrom = (($Paginator->CurrentPage() - 1) * $this->Property["RowPerPage"]) + 1) . " - " . ($RecordFrom + $this->Property["RowPerPage"] - 1) . " / {$this->RecordCount()} {$this->Property["PaginatorRecordsCaption"]}</div>
								</th>
							</tr>

							<tr class=\"Column\">" . implode(null, $ColumnTitleHTML) . "</tr>
						</thead>

						<tbody>" . implode(null, isset($DataHTML) ? $DataHTML : []) . "</tbody>
						" . ($this->Property["Footer"] ? "<tfoot><tr class=\"Footer\"><td colspan=\"99999\">{$this->Property["Footer"]}</td></tr></tfoot>" : null) . "
					</table>
				",
				"", // Submit caption
				null, // Signature modifier
				null, // Title
				null, // Header
				null, // Footer
				null, // Status
				"DatagridForm_{$this->Property["ID"]}",
				false, // Show reset button
				null, // Button content
				null, // Event handler JavaScript
				null, // Input validation
				"Datagrid" // CSS selector
			);

			$this->Property[__FUNCTION__] = $Form->HTML();
		}

		$Result = $this->Property[__FUNCTION__];

		return $Result;
    }
    #endregion Property
}
?>