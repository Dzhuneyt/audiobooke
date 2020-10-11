import {Component, EventEmitter, Input, OnDestroy, OnInit, Output} from '@angular/core';
import {Subject} from "rxjs";
import {debounceTime} from "rxjs/operators";

@Component({
  selector: 'app-search',
  templateUrl: './search.component.html',
  styleUrls: ['./search.component.css']
})
export class SearchComponent implements OnInit, OnDestroy {

  @Output() search: EventEmitter<string> = new EventEmitter();
  @Input() placeholder: string;

  private debouncer = new Subject();

  ngOnInit() {
    this.debouncer.pipe(
      debounceTime(500)
    ).subscribe((value: string) => {
      this.search.emit(value);
    })
  }

  public onSearchChange(value: string) {
    if (value.length >= 3 || value.length === 0) {
      this.debouncer.next(value);
    }
  }

  ngOnDestroy() {
    this.debouncer.complete();
  }

}
