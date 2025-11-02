# Chat Service Token Fixes

## Methods cần thêm `accessToken` parameter:

1. `sendNoti1(userId: string)` → `sendNoti1(userId: string, accessToken?: string)`
2. `sendNoti2(userId: string)` → `sendNoti2(userId: string, accessToken?: string)`
3. `endChat(userId: string, userData: UserData)` → `endChat(userId: string, userData: UserData, accessToken?: string)`
4. `sendNotiOW(senderId: string, subtitle: string, title: string = null)` → `sendNotiOW(senderId: string, subtitle: string, title: string = null, accessToken?: string)`
5. `searchFor(userId: string, userData: UserData, code: number)` → `searchFor(userId: string, userData: UserData, code: number, accessToken?: string)`
6. `search(userId: string, userData: UserData, code: number)` → `search(userId: string, userData: UserData, code: number, accessToken?: string)`
7. `pair(id1: string, id2: string)` → `pair(id1: string, id2: string, accessToken?: string)`
8. `noEnoughCoin(userId: string)` → `noEnoughCoin(userId: string, accessToken?: string)`
9. `errorUser(userId: string)` → `errorUser(userId: string, accessToken?: string)`
10. `block(userId: string, userData: UserData)` → `block(userId: string, userData: UserData, accessToken?: string)`
11. `coinEarn(userId: string)` → `coinEarn(userId: string, accessToken?: string)`

## API calls cần thêm `accessToken`:

Tất cả các calls dạng:
- `this.facebookApi.sendMessage(userId, message)` → add `, null, accessToken`
- `this.facebookApi.sendMessage(userId, message, persona)` → add `, accessToken`
- `this.facebookApi.sendTextMessage(userId, text)` → add `, null, accessToken`
- `this.facebookApi.sendTextMessage(userId, text, persona)` → add `, accessToken`

## Calls từ messenger.service.ts cần update:

- `this.chatService.mainMenu(userId)` → `this.chatService.mainMenu(userId, this.currentAccessToken)`
- Tất cả các calls khác tương tự

