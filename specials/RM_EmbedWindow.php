<?php

/**
 * @file
  * @ingroup RichMedia
  * 
  * RM_EmbedWindow - used for displaying various types of files inline.
 *
 * @addtogroup SpecialPage
 *
 * @author Benjamin Langguth
 */
if (!defined('MEDIAWIKI')) die();

class RMEmbedWindow extends UnlistedSpecialPage {

	/**
	 * Constructor
	 */
	function RMEmbedWindow() {
		UnlistedSpecialPage::UnlistedSpecialPage('EmbedWindow');
	}

	function execute($query) {
		$this->setHeaders();
		doSpecialEmbedWindow();
	}
}

/**
 * Entry point
 */
function doSpecialEmbedWindow() {
	global $wgRequest, $wgOut, $wgUser, $wgServer, $wgStyleVersion, $smwgRMScriptPath;

	// disable $wgOut - we'll print out the page manually, taking the
	// body created by the form, plus the necessary Javascript files,
	// and turning them into an HTML page
	$wgOut->disable();
	$form = new EmbedWindowForm( $wgRequest );
	$form->execute();

	$text = <<<END

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<head>
<style>
a:hover {
 text-decoration: underline;
}
a {
 text-decoration:none;
}
</style>
{$wgOut->getScript()}
</head>
<body>
{$wgOut->getHTML()}
</body>
</html>

END;
	print $text;
}

/**
 * implements Special:UploadWindow
 * @addtogroup SpecialPage
 */
class EmbedWindowForm {
	/**#@+
	 * @access private
	 */
	var $mTarget, $mfullResSize, $mPdfHeight, $mPdfWidth;

	/**
	 * Constructor : initialise object
	 * Get data POSTed through the form and assign them to the object
	 * @param $request Data posted.
	 */
	function EmbedWindowForm( &$request ) {
		$this->mTarget = $request->getText( 'target' );
		$this->mfullResSize = $request->getCheck('fullRes');
	}
	
	/**
	 * start doing stuff
	 * @access public
	 */
	function execute(){
		global $wgOut, $wgUser, $smwgRMScriptPath, $wgServer;
		$sk = $wgUser->getSkin();
		if (isset( $this->mTarget ) && $this->mTarget != "" ) {
			$nt = Title::newFromText($this->mTarget);
		}
		else {
			$errorText = wfMsg('smw_rm_embed_notarget');
			$this->showError($errorText);
			return;
		}
		
		$image = Image::newFromTitle($nt);
		$imagePath = $image->getURL();
		$embedWidth = $image->getWidth();
		$embedHeight = $image->getHeight();
		
		$imageToSmall = false;
		if ($embedHeight <= 500 || $embedWidth <= 700) {
			$imageToSmall = true;
		}
		
		if ( $this->mfullResSize) {
			$fullResNow = "font-weight:bold;";
			$fitNow = "";
		}
		else {
			$embedWidth = "670px;";
			$embedHeight = "450px;";
			$fullResNow = "";
			$fitNow = "font-weight:bold;";
		}
		list( $major, $minor ) = Image::splitMime( $image->getMimeType() );
		if ($major == "image") {
			if (!$imageToSmall) {
				# fullResSize and FitToWindow links for images
				$embedWindowPage = SpecialPage::getPage('EmbedWindow');
				$targetString = "target=".urlencode($this->mTarget);
				$fullResSizeString = "&fullRes=true";
				$fullResSizeURL = $embedWindowPage->getTitle()->getFullURL($targetString.$fullResSizeString);
				$fitToWindowURL = $embedWindowPage->getTitle()->getFullURL($targetString);
				$fullResText = wfMsg('smw_rm_embed_fullres');
				$fitWinText = wfMsg('smw_rm_embed_fittowindow');
				$viewText = wfMsg('smw_rm_embed_view');
				$fullRes_fit_section = <<<END
				<td style="padding-left:10px;padding-right:5px;padding-top:0px;padding-bottom:0px;color:white;font-weight:bold;width:40px">
				{$viewText}:
				</td>
				<td style="padding-left:5px;padding-right:5px;padding-top:0px;padding-bottom:0px;color:white;width:95px;;">
				<a href="{$fullResSizeURL}" rel="sameBox:true" style="color:white;{$fullResNow}">{$fullResText}</a>
				</td>
				<td style="color:white; width:5px;">|</td>
				<td style="border-right:1px dotted black;padding-left:5px;padding-right:10px;padding-top:0px;padding-bottom:0px;color:white;width:90px">
				<a href="$fitToWindowURL" rel="sameBox:true" style="color:white;{$fitNow}">{$fitWinText}</a>
				</td>
END;
			}
			else {
				$embedWidth = $image->getWidth();
				$embedHeight = $image->getHeight();
			}
			$embedObject = <<<END
			<table style="width:100%;height:90%">
				<tr><td style="text-align:center">
					<img id="embedded_object" src="{$imagePath}" style="margin-top:5pxvertical-align:middle" width="{$embedWidth}" height="{$embedHeight}"/>
				</td></tr>
			</table>
END;
		}
		else{
			# fullResSize and FitToWindow link for allother embedded objects
			$fullRes_fit_section = "";
			$embedObject = <<<END
				<embed autostart="0" showcontrols="1" showstatusbar="1" id="embedded_object" src="{$imagePath}" style="margin-top:5px;width:{$embedWidth}; height:{$embedHeight}" align="middle"/>
END;
		}

		$noEmbedMsg = wfMsg( 'smw_rm_noembed', $imagePath );
		$descText = wfMsg( 'smw_rm_embed_desctext' );
		$descLinkAlt = wfMsg( 'smw_rm_embed_desc_link', $nt->getBaseText() );
		$saveLinkText = wfMsg( 'smw_rm_embed_savetext' );
		$saveLinkAlt = wfMsg( 'smw_rm_embed_save', $nt->getBaseText() );

		$html = <<<END
		<div style="border:1px solid black;background-color:0091A6">
			<table style="padding:0px;margin:0px;width:100%" cellspacing="0" cellpadding="0">
				<tr style="top: 0px; right: 0px; bottom: 0px; left: 0px;background-color:#0091A6;margin:0px">
					<td style="left:0px;border-right:1px solid black;background-color:white;padding-left:10px;padding-right:10px;padding-top:0px;padding-bottom:0px;font-size:larger;font-weight:bold;width:80px">
						FileViewer
					</td>
					{$fullRes_fit_section}
					<td>&nbsp;</td>
					<!--<td style="border-right:1px dotted black;border-left:1px dotted black;padding-left:10px;padding-right:10px;padding-top:0px;padding-bottom:0px;color:white;font-weight:bold;width:60px;text-align:right">
						<a href="{$imagePath}" title="{$saveLinkAlt}" style="color:white">{$saveLinkText}
							<img title="{$saveLinkAlt}" src="{$wgServer}{$smwgRMScriptPath}/skins/save_icon.png" style="vertical-align:middle;border-width:0px;padding-bottom:3px;"></img>
						</a>
					</td>-->
					<td style="padding-top:0px;padding-bottom:0px;padding-left:10px;padding-right:10px;color:white;font-weight:bold;text-align:right;width:135px;;">
						<a href="{$nt->getFullURL()}" title="{$descLinkAlt}" target="_top" style="color:white">>>&nbsp;{$descText}
						</a>
					</td>
				</tr>
			</table>
		</div>
END;
		
		$html .= $embedObject;
		$wgOut->addHTML($html);

	}
	
	/**
	 * Show an Error
	 */
	function showError($description){
		global $wgOut;
		$wgOut->setPageTitle( wfMsg( "internalerror" ) );
		$wgOut->setRobotPolicy( "noindex,nofollow" );
		$wgOut->setArticleRelated( false );
		$wgOut->enableClientCache( false );
		$wgOut->addWikiText( $description );		
	}
}
?>