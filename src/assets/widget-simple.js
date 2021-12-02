class SunCarouselWidget {	
	constructor() {
		this.controlContainer = document.getElementsByClassName('Sun-Carousel-Widget-Controls')[0];
		const controls = Array.from(this.controlContainer.children);
		controls.forEach(control => {
			control.addEventListener('click', (e) => {this.scroll(e);}, false);
		});
		
		this.current = 1;
		this.margin = 20;
		this.style = this.controlContainer.getAttribute('data-style');
		
		this.display = document.getElementById('Sun-Carousel-Widget');
		if (this.display.children.length > 0) {
			this.imgs = this.display.children;
			this.imgSize = this.imgs[0].clientWidth;
		}
		
		const start = (this.imgSize + (this.margin * 2)) * -1;
		this.display.style.transform = 'translateX(-'+start+'px)';
		
		//Add timeoutID and index for auto loop, then if controls disabled, loop
		if (this.display.children.length > 0) {
			this.loop = {
				timeout:null,
				index:0,
				max:this.imgs.length - 1,
				speed:4000
			}
			if (controls.length == 0 && this.imgs.length > 0) {
				this.autoLoop();
			}
			window.addEventListener('popstate', () => {
				clearTimeout(this.loop.timeout); //Clear timeout before page changes;
			});
		}
	}
	
	scroll(e) {
		const index = parseInt(e.target.getAttribute('data-index'));
		if (this.current != index) {
			const change = (this.imgSize + (this.margin * 2)) * (index - 1);
			this.display.style.transform = 'translateX('+(change * -1)+'px)';
			this.current = index;
			this.setControlClasses(index);
		}
	}
	
	autoLoop() {
		this.loop.index = (this.loop.index == this.loop.max) ? 1 : this.loop.index + 1;
		
		const change = (this.imgSize + (this.margin * 2)) * (this.loop.index - 1);
		this.display.style.transform = 'translateX('+(change * -1)+'px)';
		
		this.loop.timeout = setTimeout(() => {
			this.autoLoop();
		}, this.loop.speed);
	}
	
	setControlClasses(newCurrent) {
		const controls = Array.from(this.controlContainer.children);
		controls.forEach(control => {
			const index = parseInt(control.getAttribute('data-index'));
			control.classList.remove('Sun-Carousel-Widget-Control-' + this.style + '-Active');
			if (newCurrent == index) {
				control.classList.add('Sun-Carousel-Widget-Control-' + this.style + '-Active');
			}
		});
	}
}
window.addEventListener('load', () => {
	const sunCarouselWidget = new SunCarouselWidget();
});