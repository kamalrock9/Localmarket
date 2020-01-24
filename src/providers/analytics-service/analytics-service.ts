import { Injectable } from '@angular/core';
import { GoogleAnalytics } from '@ionic-native/google-analytics';

@Injectable()
export class AnalyticsService {
  isInitialized: boolean = false;
  constructor(private ga: GoogleAnalytics) {
    console.log('Analytics Service Loaded');
  }
  init(id, interval?) {
    return this.ga.startTrackerWithId(id).then(() => {
      console.log('Google analytics is ready now');
      this.isInitialized = true;
    }).catch(e => console.log('Error starting GoogleAnalytics', e));
  }
  trackView(title: string, campaignUrl?: string, newSession?: string) {
    if (this.isInitialized)
      this.ga.trackView(title);
  }
  setAppVersion(appVersion) {
    if (this.isInitialized)
      this.ga.setAppVersion(appVersion);
  }
}