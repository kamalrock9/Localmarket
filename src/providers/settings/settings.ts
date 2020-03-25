import { Platform } from 'ionic-angular';
import { Injectable } from '@angular/core';
import { Storage } from '@ionic/storage';

@Injectable()
export class SettingsProvider {
  private SETTINGS_KEY: string = 'settings';

  settings: any = {};
  _readyPromise: Promise<any>;
  constructor(public storage: Storage, public platform: Platform) {
    console.log("Hello SettingsProvider");
  }
  load() {
    console.log("Loaded Setting");
    return this.storage.get(this.SETTINGS_KEY).then((val) => {
      if (val) {
        this.settings = val;
        return this.settings;
      } else {
        this.storage.set(this.SETTINGS_KEY, this.settings);
      }
    });
  }

  setSettings(data, id: string) {
    this.settings[id] = {};
    this.settings[id] = data;
    return this.save(this.settings);
  }

  save(data) {
    console.log('data saved');
    return this.storage.set(this.SETTINGS_KEY, data);
  } 

  get all() {
    return this.settings;
  }
  get cartCount() {
    return this.settings.cart_count;
  }
  get countryList() {
    let arr = [];
    for (let i in this.settings.appSettings.countries)
      arr.push({ id: i, name: this.settings.appSettings.countries[i] });
    return arr;
  }
  getState(country_id) {
    let arr = [];
    let obj = this.settings.appSettings.county_states[country_id];
    for (let i in obj)
      arr.push({ id: i, name: obj[i] });
    return arr;
  }
  get appSettings() {
    return this.settings.appSettings;
  }
  get reviewSettings(){
    return this.settings.reviewSettings || {};
  }

  get country() {
    return this.settings.country; 
  }
  get state() {
    return this.settings.state;
  }
  get postcode() {
    return this.settings.postcode;
  }
  get category() {
    return this.settings.category;
  }
  get layout() {
    return this.settings.layout;
  }
  get attributes() {
    return this.settings.attributes;
  }
  get dir() {
    return this.settings.dir;
  }
}