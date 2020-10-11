import {NgModule} from '@angular/core';
import {AudiobooksListComponent} from '../components/audiobooks-list/audiobooks-list.component';
import {AudiobookSingleComponent} from '../components/audiobook-single/audiobook-single.component';
import {RouterModule, Routes} from '@angular/router';
import {LazyLoadModule} from './lazy-load.module';
import {SearchComponent} from '../components/search/search.component';
import {MaterialModule} from './material.module';
import {TruncateModule} from '@yellowspot/ng-truncate';
import {CommonModule} from '@angular/common';
import {FlexLayoutModule} from '@angular/flex-layout';

const AUDIOBOOK_ROUTES: Routes = [
  {
    path: '',
    component: AudiobooksListComponent,
  },
  {
    path: ':id/:slug',
    component: AudiobookSingleComponent,
  },
];

@NgModule({
  imports: [
    CommonModule,
    RouterModule.forChild(AUDIOBOOK_ROUTES),
    LazyLoadModule,
    MaterialModule,
    TruncateModule,
    FlexLayoutModule.withConfig({
      ssrObserveBreakpoints: ['xs', 'lt-md'],
      useColumnBasisZero: false,
      printWithBreakpoints: ['md', 'lt-lg', 'lt-xl', 'gt-sm', 'gt-xs']
    }),
  ],
  providers: [],
  declarations: [
    // Component
    AudiobooksListComponent,
    AudiobookSingleComponent,
    SearchComponent,
  ],
  exports: [],
  bootstrap: []
})
export class AudiobookModule {
}
