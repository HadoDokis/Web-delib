<?php

//require_once 'class.contentautostyle.php';
//require_once 'class.styleconstants.php';

include_once 'phpodt.php';

class GraphicStyle extends ContentAutoStyle {
	
	private $graphicProperties;
	
	function __construct($name = '') {
    if (empty($name)) {
      $name = 'graphicstyle'.rand(100, 9999999);
    }
		parent::__construct($name);
		$this->styleElement->setAttribute('style:family', 'graphic');
                $this->styleElement->setAttribute('style:parent-style-name', 'Graphics');
		$this->graphicProperties = $this->contentDocument->createElement('style:graphic-properties');
                $this->styleElement->appendChild($this->graphicProperties);
	}

	function setGraphicPosition($position) {
		switch ($position) {
			case 'background':
				$position = 'background';
				break;
			case 'paragraph':
				$position = 'paragraph';
				break;
			default:
				throw new StyleException('Invalid graphic position value');
		}
                $this->graphicProperties->setAttribute('style:wrap', 'run-through');
                $this->graphicProperties->setAttribute('style:number-wrapped-paragraphs', 'no-limit');
                $this->graphicProperties->setAttribute('style:vertical-pos', 'top');
                $this->graphicProperties->setAttribute('style:vertical-rel', 'page');
                $this->graphicProperties->setAttribute('style:horizontal-rel', 'page');
                $this->graphicProperties->setAttribute('style:horizontal-pos', 'center');
                $this->graphicProperties->setAttribute('style:mirror', 'none');
                        
		$this->graphicProperties->setAttribute('style:run-through', $position);
	}
	
	function setGraphicAlign($align) {
		switch ($align) {
			case StyleConstants::LEFT:
				$align = 'left';
				break;
			case StyleConstants::RIGHT:
				$align = 'right';
				break;
			case StyleConstants::CENTER:
				$align = 'center';
				break;
			case StyleConstants::DISTRIBUTE_LETTER:
				$align = 'distribute-letter';
				break;
			case StyleConstants::DISTRIBUTE_SPACE:
				$align = 'distribute-space';
				break;
			default:
				throw new StyleException('Invalid ruby alignment value');
		}
		$this->rubyProperties->setAttribute('style:ruby-align', $align);
	}
	
}