<?php


namespace KaiserStudio5;


class MessageBuilder {
    const version = "1.0_Beta Modified by NOX";
    public function __construct() {

    }

    public function createGenericTemplate($elements,$qr=null) {
        if (!is_array($elements)) $elements = [$elements];
        $rep= array(
            "attachment" => array(
                "type" => "template",
                "payload" => array(
                    "template_type" => "generic",
                    "elements" => $elements
                )
            )
        );
        if(!empty($qr)&&is_array($qr)) $rep['quick_replies']=$qr;
        return $rep;
    }

    public function createButtonTemplate($text, $buttons) {
        if (!is_array($buttons)) $buttons = [$buttons];
        return (object) array(
            "attachment" => array(
                "type" => "template",
                "payload" => array(
                    "template_type" => "button",
                    "text" => $text,
                    "buttons" => $buttons
                )
            )
        );
    }
	
	public function createButtonForQuickReply($text, $payload, $image = null) {
        return (object) array(
			"content_type" => "text",
			"title" => $text,
			"payload" => $payload,
			"image_url" => $image
		);
    }
    

    public function createQuickReplyTemplate($text, $buttons) {
        if (!is_array($buttons)) $buttons = [$buttons];
        return (object) array(
			"text" => $text,
            "quick_replies" => $buttons
        );
    }

    public function createMediaTemplate($attachments) {
        return (object) array(
            "attachment" => array(
                "type" => "template",
                "payload" => array(
                    "template_type" => "media",
                    "elements" => $attachments
                )
            )
        );
    }

    public function createListTemplate($elements, $topElementStyle, $buttons = []) {
        if (!is_array($buttons)) $buttons = [$buttons];
        if (!is_array($elements)) $elements = [$elements];
        return (object) array(
            "attachment" => array(
                "type" => "template",
                "payload" => array(
                    "template_type" => "list",
                    "top_element_style" => "$topElementStyle", // LARGE | COMPACT
                    "elements" => $elements,
                    "buttons" => $buttons
                )
            )
        );
    }

    public function createAttachmentElement($attachmentType, $attachmentId, $buttons = []) {
        if (!is_array($buttons)) $buttons = [$buttons];
        return (object) array(
            "media_type" => $attachmentType,
            "attachment_id" => $attachmentId,
            "buttons" => $buttons
        );
    }

    public function createTemplateElement($title, $subtitle, $defaultAction = '', $buttons = null, $imageUrl = '') {
        
        $rep=array(
            "title" => $title,
            "subtitle" => $subtitle,
            "image_url" => $imageUrl,
            "default_action" => $defaultAction,
            "buttons" => $buttons
        );
    
         if (is_array($buttons)) $rep['buttons']=$buttons;
        return $rep;
    }
    
      public function createTemplateElementNB($title, $subtitle, $defaultAction = '', $buttons = [], $imageUrl = '') {
        if (!is_array($buttons)) $buttons = [$buttons];
    
        return (object) array(
            "title" => $title,
            "subtitle" => $subtitle,
            "image_url" => $imageUrl,
            "default_action" => $defaultAction
        );
    }

    public function createButton($type, $title, $payload = "", $url = "") {
        return (object) array_filter(array(
            "type" => $type,
            "title" => $title,
            "payload" => $payload,
            "url" => $url
        ));
    }

    public function createTemplateDefaultAction($url, $isMessengerExtension = false, $webviewHeight = "TALL") {
        return (object) array(
            "type" => "web_url",
            "url" => $url,
            "messenger_extensions" => $isMessengerExtension,
            "webview_height_ratio" => $webviewHeight
        );
    }

    public function createTextMessage($text) {
        return array(
            "text" => $text
        );
    }
	// New Addon
    public function createUploadFile($type, $attachments) {
        return (object) array(
            "attachment" => array(
                "type" => $type,
                "payload" => array(
                    "url" => $attachments
                )
            )
        );
    }
}