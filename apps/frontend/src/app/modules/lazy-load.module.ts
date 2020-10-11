import {NgModule} from '@angular/core';
import {LazyLoadComponent} from '../components/lazy-load/lazy-load.component';
import {MaterialModule} from './material.module';
import {FlexLayoutModule} from '@angular/flex-layout';

@NgModule({
  imports: [
    MaterialModule,
    FlexLayoutModule,
  ],
  providers: [],
  declarations: [
    LazyLoadComponent
  ],
  exports: [
    LazyLoadComponent
  ],
  bootstrap: []
})
export class LazyLoadModule {
}
