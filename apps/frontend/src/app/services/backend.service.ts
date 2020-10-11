import {Observable, throwError as observableThrowError} from 'rxjs';
import {Injectable} from '@angular/core';
import {HttpClient, HttpHeaders} from '@angular/common/http';
import {environment} from '../../environments/environment';
import {AccessToken} from './user.service';
import {LocalStorageService} from 'angular-2-local-storage';
import {Router} from '@angular/router';
import {catchError, map} from 'rxjs/operators';

@Injectable()
export class BackendService {

  public static readonly METHOD_GET = 'GET';
  public static readonly METHOD_POST: 'POST';

  constructor(protected http: HttpClient,
              private router: Router,
              private localStorageService: LocalStorageService) {
  }

  public request(path, method = BackendService.METHOD_GET, params = {}, queryParams = {}): Observable<any> {
    const url = (environment.backendUrl ? environment.backendUrl + '/' : '') + path;

    if (method === BackendService.METHOD_GET) {
      queryParams = Object.assign({}, params, queryParams);
    }

    const accessToken: AccessToken = this.localStorageService.get('access_token');

    const headers = {};
    if (accessToken) {
      headers['Authorization'] = 'Bearer ' + accessToken.access_token;
    } else {
    }

    const httpOptions = {
      headers: new HttpHeaders(headers),
      body: params,
      params: queryParams
    };

    return this.http.request(method, url, httpOptions)
      .pipe(map((res) => {
        // console.log('API success for URL', path, JSON.stringify(res));
        return res;
      }))
      .pipe(
        catchError(err => {
          console.error('API call for ' + url + ' returned an error: ', err['status']);
          console.error(JSON.stringify(err));

          if (err['status'] === 401) {
            this.localStorageService.set('last_url', this.router.url);
            this.localStorageService.remove('access_token');
            this.router.navigate(['user/login']);
          }

          return observableThrowError(err);
        })
      );
  }
}
