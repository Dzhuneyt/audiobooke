import {TestBed} from '@angular/core/testing';

import {AnalyticsService} from './analytics.service';
import {RouterTestingModule} from '@angular/router/testing';

describe('AnalyticsService', () => {
  beforeEach(() => TestBed.configureTestingModule({
    providers: [],
    imports: [
      RouterTestingModule.withRoutes([]),
    ]
  }));

  it('should be created', () => {
    const service: AnalyticsService = TestBed.get(AnalyticsService);
    expect(service).toBeTruthy();
  });
});
