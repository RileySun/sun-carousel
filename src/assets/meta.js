class SunCarouselMeta {
	constructor() {
		//set global media (wp.media is a pain)
		this.media = null;
    	
    	//Add Button
		document.getElementById("Sun-Carousel-Add").addEventListener("click", (e) => {
			this.openModal(e, this);
		}, false);
		
		//Remove Existing
		const imgs = Array.from(document.getElementsByClassName('Sun-Carousel-IMG-Remove'));
		imgs.forEach(img => {
			img.addEventListener('click', this.removeImage, false);
		});
		
		//On radio update, update shortcode input
		const radios = Array.from(document.getElementsByClassName('Sun-Carousel-Radio'));
		radios.forEach(radio => {
			radio.addEventListener('change', this.updateShortcode, false);
		});
		
		//Add shortcode copy paste
		document.getElementById('Sun-Carousel-Shortcode-Input').addEventListener('click', this.selectShortcode, false);
		document.getElementById('Sun-Carousel-Shortcode-Copy').addEventListener('click', this.copyShortcode, false);
		
	}
	
	openModal(e, self) {
		e.preventDefault();
		self.media = wp.media({title: 'Upload Image', multiple: false}).open().on('select', () => {self.selectMedia();});
	}
	
	selectMedia() {
		const mediaState = this.media;
		if (typeof mediaState !== "undefined") {
			const image = mediaState.state().get('selection').first();
			const id = image.attributes.id;
			const sizes = image.changed.sizes;
			const thumbnail = sizes[Object.keys(sizes)[0]].url;
			const input = document.getElementById('Sun-Carousel-Input');
			
			if (input.value.length > 0 && input.value !== 'null') {
				const data = JSON.parse(input.value);
				data.push(id);
				input.value = JSON.stringify(data);
			}
			else {
				const data = [id]
				input.value = JSON.stringify(data);
			}
			this.addImage(id, thumbnail);
		}
	}
	
	addImage(imgID, imgURL) {
		const container = document.createElement('div');
		const img = document.createElement('img');
		const remove = document.createElement('div');
		const content = document.getElementById('Sun-Carousel-Images');
		
		container.classList.add('Sun-Carousel-IMG-Container');
		img.classList.add('Sun-Carousel-IMG');
		remove.classList.add('Sun-Carousel-IMG-Remove');
		
		container.setAttribute('data-id', imgID)
		img.src = imgURL;
		remove.textContent = 'X';
		remove.addEventListener('click', this.removeImage, false);
		
		container.appendChild(img);
		container.appendChild(remove);
		content.insertBefore(container, content.children[content.children.length - 1]);
	}
	
	removeImage(e) {
		//Elements
		const parent = e.target.parentElement;
		const input = document.getElementById('Sun-Carousel-Input');
		
		//Data
		const imgID = parent.getAttribute('data-id');
		const current = JSON.parse(input.value);
		const index = current.indexOf(parseInt(imgID));
		
		//Action
		if (index != -1) {
			current.splice(index, 1);
			input.value = JSON.stringify(current);
			parent.parentElement.removeChild(parent);
		}
	}
	
	selectShortcode(e) {
		e.target.select();
		e.target.setSelectionRange(0, 99999);
	}
	
	copyShortcode() {
		const input = document.getElementById('Sun-Carousel-Shortcode-Input');
		navigator.clipboard.writeText(input.value);
		
		document.getElementById('Sun-Carousel-Notif').style.display = 'block';
		setTimeout(() => {
			document.getElementById('Sun-Carousel-Notif').style.display = 'none';
		}, 3000);
	}
	
	updateShortcode() {
		const input = document.getElementById('Sun-Carousel-Shortcode-Input');
		
		//const type = (document.getElementById('Simple').checked) ? 'Simple' : 'Advanced';
		//const controls = (document.getElementById('Enabled').checked) ? 'Enabled' : 'Disabled';
		//const style = (document.getElementById('Light').checked) ? 'Light' : 'Dark';
		//const shortcode = '[sun-carousel id="' + id + '" type="' + type + '" controls="' + controls + '" style="' + style + '"]';
		
		const id = document.getElementById('Sun-Carousel').getAttribute('data-postID');
		const shortcode = '[sun-carousel id="' + id + '"]';
		input.value = shortcode;
	}
}
window.addEventListener('load', () => {
	const sunCarousel = new SunCarouselMeta();
});