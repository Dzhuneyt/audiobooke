import {AfterViewInit, Component, ElementRef, Input} from '@angular/core';
import {Observable} from 'rxjs';

@Component({
  selector: 'app-lazy-load',
  templateUrl: './lazy-load.component.html',
  styleUrls: ['./lazy-load.component.css']
})
export class LazyLoadComponent implements AfterViewInit {

  @Input() nextPageCallback: () => Observable<boolean>;
  public isInViewPort = false;
  private lazyLoadInProgress = null;
  private isComponentLoaded = false;

  constructor(private element: ElementRef) {
  }

  static isElementInViewport(el) {
    const rect = el.getBoundingClientRect();

    return (
      rect.top >= 0 &&
      rect.left >= 0 &&
      rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && /*or $(window).height() */
      rect.right <= (window.innerWidth || document.documentElement.clientWidth) /*or $(window).width() */
    );
  }


  ngAfterViewInit() {
    this.isComponentLoaded = true;
  }

  public loadMore() {
    this.nextPageCallback().subscribe((result: boolean) => {
      // this.isInViewPort = false;
    });
  }

  // Removed for now because it leads to bad UX
  // @HostListener('window:scroll', ['$event'])
  // onScroll(event) {
  //   if (!this.isComponentLoaded) {
  //     // Too early for anything
  //     return;
  //   }
  //
  //   if (this.lazyLoadInProgress) {
  //     // Another scroll event was triggered too soon
  //     // (before the previous one was handled)
  //     return;
  //   }
  //
  //   this.lazyLoadInProgress = setTimeout(() => {
  //     const lazyLoaderIsVisible = LazyLoadComponent.isElementInViewport(this.element.nativeElement);
  //     if (lazyLoaderIsVisible) {
  //       this.isInViewPort = true;
  //       console.log('Lazy load triggers callback');
  //       this.nextPageCallback().subscribe((result: boolean) => {
  //         this.isInViewPort = false;
  //       });
  //     }
  //
  //     // Cleanup so further scroll events can potentially trigger new callbacks
  //     this.lazyLoadInProgress = null;
  //   }, 500);
  // }


}
