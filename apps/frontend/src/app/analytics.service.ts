import {Injectable, PLATFORM_ID} from '@angular/core';
import {environment} from '../environments/environment';
import {NavigationEnd, Router} from '@angular/router';
import {isPlatformBrowser} from '@angular/common';

declare var gtag: Function;

@Injectable({
  providedIn: 'root'
})
export class AnalyticsService {

  constructor(private router: Router) {

  }

  public event(eventName: string, params: {}) {
    if (!isPlatformBrowser(PLATFORM_ID)) {
      return;
    }
    gtag('event', eventName, params);
  }

  public init() {
    if (!isPlatformBrowser(PLATFORM_ID)) {
      return;
    }

    this.listenForRouteChanges();

    try {

      console.log('Initializing Google Analytics for key: ' + environment.googleAnalyticsKey);

      const script1 = document.createElement('script');
      script1.async = true;
      script1.src = 'https://www.googletagmanager.com/gtag/js?id=' + environment.googleAnalyticsKey;
      document.head.appendChild(script1);

      const script2 = document.createElement('script');
      script2.innerHTML = `
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '` + environment.googleAnalyticsKey + `', {'send_page_view': false});
      `;
      document.head.appendChild(script2);
    } catch (ex) {
      console.error('Error appending google analytics');
      console.error(ex);
    }
  }

  private listenForRouteChanges() {
    this.router.events.subscribe(event => {
      if (event instanceof NavigationEnd) {
        gtag('config', environment.googleAnalyticsKey, {
          'page_path': event.urlAfterRedirects,
        });
        console.log('Sending Google Analytics hit for route', event.urlAfterRedirects);
        console.log('Property ID', environment.googleAnalyticsKey);
      }
    });
  }
}
