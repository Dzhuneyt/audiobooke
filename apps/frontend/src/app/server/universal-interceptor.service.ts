import {Inject, Injectable, Optional} from '@angular/core';
import {HttpHandler, HttpInterceptor, HttpRequest} from '@angular/common/http';
import {Request} from 'express';
import {REQUEST} from '@nguniversal/express-engine/tokens';
import {PLATFORM_ID} from '@angular/core';
import {isPlatformServer} from '@angular/common';

@Injectable()
export class UniversalInterceptor implements HttpInterceptor {

  constructor(
    @Optional() @Inject(REQUEST) protected request: Request,
    @Inject(PLATFORM_ID) private platformId,
  ) {
  }

  intercept(req: HttpRequest<any>, next: HttpHandler) {
    let serverReq: HttpRequest<any> = req;
    if (this.request && isPlatformServer(this.platformId)) {
      console.log('Making backend API call in the Angular Universal')
      // let newUrl = `${this.request.protocol}://${this.request.get('host')}:${this.request.get('port')}`;
      let newUrl = `http://backend`;
      if (!req.url.startsWith('/')) {
        newUrl += '/';
      }
      newUrl += req.url;
      serverReq = req.clone({url: newUrl});
    }
    return next.handle(serverReq);
  }
}
