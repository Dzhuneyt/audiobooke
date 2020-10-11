import {async, ComponentFixture, TestBed} from '@angular/core/testing';

import {PrivacyPolicyComponent} from './privacy-policy.component';
import {RouterTestingModule} from "@angular/router/testing";
import {Component} from "@angular/core";

@Component({
  template: ''
})
class FakeComponent {
}

describe('PrivacyPolicyComponent', () => {
  let component: PrivacyPolicyComponent;
  let fixture: ComponentFixture<PrivacyPolicyComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [PrivacyPolicyComponent, FakeComponent],
      imports: [
        RouterTestingModule.withRoutes([]),
      ],
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PrivacyPolicyComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  afterEach(() => {
    fixture.destroy();
    document.body.removeChild(fixture.debugElement.nativeElement);
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
