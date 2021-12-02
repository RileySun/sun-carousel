<?php

class CarouselWidget {
	var $ID;
	var $type;
	var $controls;
	var $style;

	function __construct($idAtt) {
		
		add_action('enqueue_scripts', array( __CLASS__, 'carouselWidgetEnqueScripts'));
		$this->ID = $idAtt;
		$this->img = get_post_meta($this->ID, "Carousel-IMG", true);
		$this->type = get_post_meta($this->ID, "Carousel-Type", true);
		$this->controls = get_post_meta($this->ID, "Carousel-Controls", true);
		$this->style = get_post_meta($this->ID, "Carousel-Style", true);
		$this->size = get_post_meta($this->ID, "Carousel-Size", true);
	}
	
	function simpleWidget() {
		wp_enqueue_style('sun-carousel-widget-simple-style');
		wp_enqueue_script('sun-carousel-widget-simple-js');
		
		$carouselImages = json_decode($this->img);
		$title = get_the_title($this->ID);
		
		$count = 0;
		$imgs = '';
		$controls = '';
		$mid = 1;
		
		$imageSize = '';
		switch ($this->size) {
			case 'Small':
				$imageSize = 'thumbnail';
				break;
			case 'Medium':
				$imageSize = 'medium';
				break;
			case 'Large':
				$imageSize = 'large';
				break;
			default:
				$imageSize = 'thumbnail';
		}
		
		forEach($carouselImages as &$imgID) {
			$imgURL = wp_get_attachment_image_url($imgID, $imageSize);
			$imgs .= '<IMG class="Sun-Carousel-Widget-IMG" src="'.$imgURL.'" />';
			
			if ($this->controls == 'Enabled' && $count > 0 && $count < sizeOf($carouselImages) - 1) {
				$controlClass = ($count == $mid) ? ' Sun-Carousel-Widget-Control-'.$this->style.'-Active' : '';
				$controls .= '<div class="Sun-Carousel-Widget-Control-'.$this->style.' '.$controlClass.'" data-index="'.$count.'"></div>';
			}
			$count += 1;
		}
		//<h2 class="Sun-Carousel-Widget-Title">'.$title.'</h2>
		return '
			<div class="Sun-Carousel-Widget-Simple">
				<div class="Sun-Carousel-Widget-Container">
					<div id="Sun-Carousel-Widget" class="Sun-Carousel-Widget-Content" >'.$imgs.'</div>
				</div>
				<div class="Sun-Carousel-Widget-Controls" data-style="'.$this->style.'">'.$controls.'</div>
			</div>
		';
	}
	
	function advancedWidget() {
		wp_enqueue_style('sun-carousel-widget-advanced-style');
		wp_enqueue_script('sun-carousel-widget-advanced-js');
		
		$carouselImages = json_decode($this->img);
		$title = get_the_title($this->ID);
		
		$count = 0;
		$imgs = '';
		$controls = '';
		$last = sizeOf($carouselImages) - 1;
		
		forEach($carouselImages as &$imgID) {
			$imgURL = wp_get_attachment_image_url($imgID, 'thumbnail');
			
			$imgClass = 'Sun-Carousel-Widget-Default';
			
			switch($count) {
				case 0:
					$imgClass = 'Sun-Carousel-Widget-Current';
					break;
				case 1:
					$imgClass = 'Sun-Carousel-Widget-Next';
					break;
				case $last:
					$imgClass = 'Sun-Carousel-Widget-Prev';
					break;
				default:		
			}
			
			$imgs .= '<IMG class="Sun-Carousel-Widget-IMG '.$imgClass.'" src="'.$imgURL.'" />';
			
			if ($this->controls == 'Enabled') {
				$controlClass = ($count == 0) ? ' Sun-Carousel-Widget-Control-Active' : '';
				$controls .= '<div class="Sun-Carousel-Widget-Control'.$controlClass.'" data-index="'.$count.'"></div>';
			}
			$count += 1;
		}

		return '
			<div class="Sun-Carousel-Widget-Simple">
				<div class="Sun-Carousel-Widget-Container">
					<div id="Sun-Carousel-Widget" class="Sun-Carousel-Widget-Content">'.$imgs.'</div>
				</div>
				<div class="Sun-Carousel-Widget-Controls">
					'.$controls.'
				</div>
			</div>
		';
	}
	
	public function render() {
		return ($this->type == 'Simple') ? $this->simpleWidget() : $this->advancedWidget();
	}
}