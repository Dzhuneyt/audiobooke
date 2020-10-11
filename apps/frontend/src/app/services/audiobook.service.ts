import {Injectable} from '@angular/core';
import {BackendService} from './backend.service';

import {Observable} from 'rxjs';
import {map} from 'rxjs/operators';
import * as prettyMs from 'pretty-ms';

@Injectable()
export class AudiobookService {

  constructor(protected backendService: BackendService) {
  }

  static formatSecondsToHumanReadable(seconds: number) {
    return prettyMs(seconds * 1000);
  }

  public getAudiobooks(page = 1, params = {}): Observable<any> {
    return this
      .backendService
      .request(
        'v1/audiobooks',
        BackendService.METHOD_GET,
        params,
        {page: page}
      );
  }

  public getTop10() {
    return this.backendService.request('v1/audiobooks/topten',
      BackendService.METHOD_GET).pipe(map(value => value['topten']));
  }

  public getLatestAuthors(): Observable<any[]> {
    return this.backendService.request('v1/authors/latest', BackendService.METHOD_GET);
  }

  public request(endpoint: string, method = 'get', params: Object = {}, queryParams: Object = {}): Observable<any> {
    return this.backendService.request('v1/audiobooks/' + endpoint, method, params, queryParams);
  }

  public getAudioBook(id: number): Observable<any> {
    return this.backendService.request('v1/audiobooks/' + id, BackendService.METHOD_GET);
  }

  public getAudiobookDownloadUrl(id: number): Observable<string> {
    return this.backendService
      .request('v1/audiobooks/download/' + id, BackendService.METHOD_GET)
      .pipe(
        map(res => res['download_url'])
      );
  }
}
