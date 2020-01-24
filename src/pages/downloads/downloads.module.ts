import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { DownloadsPage } from './downloads';

@NgModule({
  declarations: [
    DownloadsPage,
  ],
  imports: [
    IonicPageModule.forChild(DownloadsPage),
    TranslateModule.forChild()
  ],
  providers: [ 
    
  ]
})
export class DownloadsPageModule {}
