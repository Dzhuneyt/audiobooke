import {Injectable} from '@angular/core';
import {BehaviorSubject, Observable} from 'rxjs';
import {BackendService} from './backend.service';
import {LocalStorageService} from 'angular-2-local-storage';
import {map} from 'rxjs/operators';
import {AnalyticsService} from '../analytics.service';

export interface AccessToken {
  access_token: string;
  created_at: string;
  expires: string;
  id_user: number;
}

interface UserState {
  isLoggedIn: boolean;
  accessToken?: AccessToken;
}

@Injectable()
export class UserService {

  public user = new BehaviorSubject<UserState>({
    isLoggedIn: false
  });

  constructor(
    private backendService: BackendService,
    private localStorageService: LocalStorageService,
    private analytics: AnalyticsService,
  ) {

    const previousAccessToken = this.getAccessToken();
    if (previousAccessToken) {
      this.user.next({
        isLoggedIn: true,
        accessToken: previousAccessToken,
      });
    }
  }

  getGoogleSsoAuthUrl(): Observable<string> {
    const params = {};
    params['domain'] = location.protocol + '//' + location.hostname;

    return this.backendService.request(
      'v1/user/sso',
      BackendService.METHOD_GET,
      params,
      {provider: 'googleplus'}
    ).pipe(map(res => res['auth_url']));
  }

  finishGoogleSsoAuth(param: { code: (string | any); state: (string | any) }): Promise<boolean> {

    param['domain'] = location.protocol + '//' + location.hostname;

    return new Promise((resolve, reject) => {
      this.backendService.request(
        'v1/user/sso',
        BackendService.METHOD_GET,
        param,
        {provider: 'googleplus'}
      ).subscribe((res: AccessToken) => {
        if (res) {
          this.user.next({
            isLoggedIn: true,
            accessToken: res,
          });

          this.setAccessToken(res);

          this.analytics.event('login', {
            method: 'google',
          });
          resolve(true);
        } else {
          reject(false);
        }
      }, err => {
        reject(err);
      });
    });
  }

  public isLoggedIn(): boolean {
    return !!this.getAccessToken();
  }

  public getAccessToken(): AccessToken {
    return this.localStorageService.get('access_token');
  }

  public setAccessToken(token: AccessToken) {
    return this.localStorageService.set('access_token', token);
  }

  public logout() {
    this.localStorageService.remove('access_token');
  }
}
