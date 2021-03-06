/*
 * Copyright (C) 2017 ZeXtras SRL
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, version 2 of
 * the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License.
 * If not, see <http://www.gnu.org/licenses/>.
 */

import {ZmAttachDialog} from "../zimbra/zimbraMail/share/view/dialog/ZmAttachDialog";
import {ZmMsg} from "../zimbra/zimbraMail/ZmMsg";
import {ZimbraDriveTabView} from "./ZimbraDriveTabView";
import {AjxCallback} from "../zimbra/ajax/boot/AjxCallback";
import {ZmComposeController} from "../zimbra/zimbraMail/mail/controller/ZmComposeController";

export class ZimbraDriveAttachDialog extends ZmAttachDialog {

  public static CLASSNAME: string = "ZimbraDriveAttachDialog";
  private _driveView: ZimbraDriveTabView;
  private _currentComposeController: ZmComposeController;

  public getDriveView(composeController: ZmComposeController): ZimbraDriveTabView {
    this.setTitle(ZmMsg.attachFile);

    this._currentComposeController = composeController;
    if (!this._driveView) {
      this._driveView = new ZimbraDriveTabView(this);
    }
    this._driveView.reparentHtmlElement(this._getContentDiv().childNodes[0], 0);
    this.setOkListener(new AjxCallback(this, this._okButtonListener));
    this.setCancelListener(new AjxCallback(this, this.popdown));

    this._driveView.showFolderContent();
    return this._driveView;
  }

  private _okButtonListener(): void {
    this._driveView.uploadFiles(this._currentComposeController);
    this.popdown();
  }
}