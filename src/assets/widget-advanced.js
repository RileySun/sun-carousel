class SunCarouselWidget {	
	constructor() {
		const controls = Array.from(document.getElementsByClassName('Sun-Carousel-Widget-Control'));
		controls.forEach(control => {
			control.addEventListener('click', (e) => {this.change(e);}, false);
		});
		this.imgs = document.getElementById('Sun-Carousel-Widget').children;
		this.current = 0;
		
		
		//Add timeoutID and index for auto loop, then if controls disabled, loop
		if (this.imgs.length > 0) {
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
				clearTimeout(this.loop.timeout);
			});
		}
	}
	
	change(e) {
		const index = parseInt(e.target.getAttribute('data-index'));
		if (this.current != index) {
			this.current = index;
			this.setIMGClasses(index);
			this.setControlClasses(index);
		}
	}
	
	autoLoop() {
		this.loop.index = (this.loop.index == this.loop.max) ? 1 : this.loop.index + 1;
		this.current = this.loop.index;
		this.setIMGClasses(this.loop.index);
		this.setControlClasses(this.loop.index);
		
		this.loop.timeout = setTimeout(() => {
			this.autoLoop();
		}, this.loop.speed);
	}
	
	setIMGClasses(newIndex) {
		//Reset Classes
		const imgs = Array.from(document.getElementsByClassName('Sun-Carousel-Widget-IMG'));
		imgs.forEach(img => {
			img.classList.remove('Sun-Carousel-Widget-Prev');
			img.classList.remove('Sun-Carousel-Widget-Current');
			img.classList.remove('Sun-Carousel-Widget-Next');
			img.classList.add('Sun-Carousel-Widget-Default');
		});
		
		//Last & Helper Function
		const last = imgs.length - 1;
		const setClass = (prev, current, next) => {
			imgs[prev].classList.remove('Sun-Carousel-Widget-Default');
			imgs[current].classList.remove('Sun-Carousel-Widget-Default');
			imgs[next].classList.remove('Sun-Carousel-Widget-Default');
			
			imgs[prev].classList.add('Sun-Carousel-Widget-Prev');
			imgs[current].classList.add('Sun-Carousel-Widget-Current');
			imgs[next].classList.add('Sun-Carousel-Widget-Next');
		}
		
		//Set Classes
		switch (newIndex) {
			case 0:
				setClass(last, 0, 1);
				break;
			case last:
				setClass(last - 1, last, 0);
				break;
			default:
				setClass(newIndex - 1, newIndex, newIndex + 1);
		}
	}
	
	setControlClasses(newIndex) {
		const controls = Array.from(document.getElementsByClassName('Sun-Carousel-Widget-Control'));
		controls.forEach(control => {
			const index = parseInt(control.getAttribute('data-index'));
			control.classList.remove('Sun-Carousel-Widget-Control-Active');
			if (newIndex == index) {
				control.classList.add('Sun-Carousel-Widget-Control-Active');
			}
		});
	}
}
window.addEventListener('load', () => {
	const sunCarouselWidget = new SunCarouselWidget();
});