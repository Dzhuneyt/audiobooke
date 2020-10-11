import {Component, OnInit} from '@angular/core';
import {AudiobookService} from '../../services/audiobook.service';
import {ActivatedRoute} from '@angular/router';
import {Title} from '@angular/platform-browser';
import { MatSnackBar } from '@angular/material/snack-bar';
import {AnalyticsService} from '../../analytics.service';
import {AudiobookModel} from '../../models/audiobook.model';

@Component({
  selector: 'app-audiobook-single',
  templateUrl: './audiobook-single.component.html',
  styleUrls: ['./audiobook-single.component.scss']
})
export class AudiobookSingleComponent implements OnInit {

  public details: AudiobookModel;

  constructor(
    private route: ActivatedRoute,
    private audiobookService: AudiobookService,
    private titleService: Title,
    private snackBar: MatSnackBar,
    private analytics: AnalyticsService,
  ) {
  }

  get yearsAgo() {
    return (new Date().getFullYear()) - this.details.year;
  }

  getSecondsFormatted() {
    if (!this.details) {
      return 0;
    }

    return AudiobookService.formatSecondsToHumanReadable(this.details.total_seconds);
  }

  ngOnInit() {
    this.route.params.subscribe(params => {
      const id = +params['id']; // (+) converts string 'id' to a number

      this.audiobookService.getAudioBook(id).subscribe((details: AudiobookModel) => {
        this.details = details;


        this.titleService.setTitle(details.title + ', an Audiobook by ' + details.author_name + ', Audiobooke');
      });
    });

  }

  download(id: number) {
    this.audiobookService.getAudiobookDownloadUrl(id).subscribe((url: string) => {
      window.location.replace(url);
    });
  }

  favorite(id: number) {

    this.audiobookService.request('favorite/' + id, 'PUT').subscribe(res => {
      console.log(res);
      this.snackBar.open('Added to favorites', null, {
        panelClass: 'success',
        duration: 2000,
      });
      this.details.is_favorited = true;

      this.analytics.event('add_to_wishlist', {
        items: [
          {
            id: this.details.id,
            name: this.details.title,
          }
        ],
      });
    }, error1 => {
      console.error(error1);
      this.snackBar.open('Failed to add to favorites', null, {
        panelClass: 'error',
        duration: 2000,
      });
    });
  }

  audibleRedirect() {
    window.location.replace(this.details.audible_url);
  }
}
