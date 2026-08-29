import { Component, EnvironmentInjector} from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonicModule } from '@ionic/angular';
import { mainMenu } from '../models/menus';
import { appCopyright, appVersion } from 'src/assets/config/config';
import { TabsService } from '../services/tabs/tabs.service';


@Component({
  selector: 'app-tabs',
  templateUrl: './tabs.page.html',
  styleUrls: ['./tabs.page.scss'],
  standalone: true,
  imports: [IonicModule,CommonModule],
})
export class TabsPage {

  appVersion: string;
  appCopyright: string;

  get tabs(): mainMenu[] {
    return this._tabs.getTabs();
  }

  constructor(private _tabs:TabsService,public environmentInjector: EnvironmentInjector) {
    this.appVersion = appVersion;
    this.appCopyright = appCopyright;
    console.log('appCopyright',this.appVersion);
  }
}
