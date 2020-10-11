import {Component, Inject, OnInit, PLATFORM_ID} from '@angular/core';
import {AudiobookService} from '../../services/audiobook.service';
import {Title} from '@angular/platform-browser';
import {map, take} from 'rxjs/operators';
import {Observable, Observer} from 'rxjs';
import {AnalyticsService} from '../../analytics.service';
import {AudiobookModel} from '../../models/audiobook.model';
import {isPlatformServer} from '@angular/common';
import {ActivatedRoute, Router} from '@angular/router';


@Component({
  selector: 'app-audiobooks-list',
  templateUrl: './audiobooks-list.component.html',
  styleUrls: ['./audiobooks-list.component.scss']
})
export class AudiobooksListComponent implements OnInit {

  public list: AudiobookModel[] = [];
  public totalCount: number;
  public isLoading = false;
  public search_text = null;
  public currentPage: number = undefined;

  constructor(
    @Inject(PLATFORM_ID) private platformId: string,
    private audiobookService: AudiobookService,
    private titleService: Title,
    private analytics: AnalyticsService,
    private router: Router,
    private activatedRoute: ActivatedRoute,
  ) {
  }

  public isServer() {
    return isPlatformServer(this.platformId);
  }

  ngOnInit() {
    // Listen for changes of the query parameter "page" and call API again
    this.activatedRoute.queryParams.subscribe(value => {
      const page = value.hasOwnProperty('page') ? value.page : 1;
      const searchKeyword = value.hasOwnProperty('search') ? value.search : undefined;

      let needsRefresh = false;

      if (page !== this.currentPage) {
        // Page has changed
        this.currentPage = page;
        // Also triggered on initial component load

        needsRefresh = true;
      }

      if (searchKeyword || this.search_text) {
        // Search is present as query param or was present as query param and is now not present
        if (searchKeyword !== this.search_text) {
          // Search keyword was changed
          this.search_text = searchKeyword;

          this.titleService.setTitle('Audiobooke - Search results for "' + this.search_text + '"');

          // Reset pagination to first page on change of search keyword
          this.currentPage = 1;

          needsRefresh = true;
        }
      } else {
        this.titleService.setTitle('Audiobooke - Audiobooks list');
      }

      if (!needsRefresh) {
        return;
      }

      this.fetchPage(this.currentPage).subscribe(success => {

      });
    });
    // this.activatedRoute.params.subscribe((res: Object) => {
    //   if (res.hasOwnProperty('keyword')) {
    //     this.titleService.setTitle('Audiobooke - Search results for "' + res['keyword'] + '"');
    //   } else {
    //     this.titleService.setTitle('Audiobooke - Audiobooks list');
    //   }
    // });
  }

  public navigateToPage(page: number) {
    this.router.navigate(['/audiobook'], {
      queryParams: {
        search: this.search_text,
        page: page
      }
    });
  }

  public searchInputListener(value: string) {
    const queryParams = {};
    if (value) {
      queryParams['search'] = value;
    }
    if (this.currentPage) {
      queryParams['page'] = this.currentPage;
    }
    this.router.navigate(['/audiobook'], {
      queryParams,
    });
  }

  public search(value: string) {
    if (this.search_text !== value) {
      this.search_text = value;
      // Flush the current items and start new pagination + API calls
      this.list = [];
      this.currentPage = 1;
      this.fetchPage(1).subscribe(() => {
        this.analytics.event('search', {
          search_term: this.search_text
        });
      });
    }
  }

  public generateSlug(string: string) {
    return string.toString().toLowerCase()
      .replace(/\s+/g, '-')           // Replace spaces with -
      .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
      .replace(/--+/g, '-')         // Replace multiple - with single -
      .replace(/^-+/, '')             // Trim - from start of text
      .replace(/-+$/, '');            // Trim - from end of text
  }

  private fetchPage(pageToFetch: number) {
    return new Observable((observer: Observer<boolean>) => {
      this.isLoading = true;

      const filters = {};
      if (this.search_text) {
        filters['search'] = this.search_text;
      }

      this.audiobookService.getAudiobooks(
        pageToFetch,
        filters
      ).pipe(
        map(res => {

          this.totalCount = parseInt(res['_meta']['totalCount'], 10);

          return res['items'];
        }),
        take(1)
      ).subscribe((list: AudiobookModel[]) => {

        this.list = list;

        // Hide spinner
        this.isLoading = false;

        observer.next(true);
        observer.complete();
      }, err => {
        // console.log(err);
        observer.error(err);
      });
    });
  }

  private fetchNextPage(): Observable<boolean> {
    const pageToFetch = this.currentPage + 1;
    return this.fetchPage(pageToFetch);
  }
}
