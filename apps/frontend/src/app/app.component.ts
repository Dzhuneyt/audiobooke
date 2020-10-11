import {Component, OnInit} from '@angular/core';
import {AnalyticsService} from './analytics.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent implements OnInit {

  constructor(
    private analytics: AnalyticsService
  ) {

  }

  // @TODO implement user menu
  public drawerOpen = false;

  ngOnInit() {
    this.analytics.init();
  }

  public drawerClicked() {
    this.drawerOpen = !this.drawerOpen;
  }
}
