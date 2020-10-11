import {async, ComponentFixture, TestBed} from '@angular/core/testing';

import {UserLoginComponent} from './user-login.component';
import {NO_ERRORS_SCHEMA} from "@angular/core";
import {UserService} from "../../services/user.service";
import {RouterTestingModule} from "@angular/router/testing";

class MockUserService {

}

describe('UserLoginComponent', () => {
  let component: UserLoginComponent;
  let fixture: ComponentFixture<UserLoginComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [UserLoginComponent],
      imports: [
        RouterTestingModule,
      ],
      providers: [
        {
          provide: UserService,
          useClass: MockUserService,
        },
      ],
      schemas: [
        NO_ERRORS_SCHEMA
      ]
    })
      .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(UserLoginComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
