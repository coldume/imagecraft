# PhpGd Engine

## EventListeners

| Event          | PRI   | Ext    | Listener                    | Method                | PPG |
| :------------- | :---- | :----- | :-------------------------- | :-------------------- | :-- |
| `PRE_IMAGE`    | `999` | `Core` | `SystemRequirementListener` | `verifyEngine`        |     |
| `PRE_IMAGE`    | `989` | `Core` | `SystemRequirementListener` | `verifySavedFormat`   |     |
| `PRE_IMAGE`    | `909` | `Core` | `ImageAwareLayerListener`   | `initImcUri`          |     |
| `PRE_IMAGE`    | `899` | `Core` | `ImageAwareLayerListener`   | `initFilePointer`     |     |
| `PRE_IMAGE`    | `889` | `Core` | `ImageAwareLayerListener`   | `initImageInfo`       |     |
| `PRE_IMAGE`    | `879` | `Core` | `ImageAwareLayerListener`   | `initFinalDimensions` |     |
| `PRE_IMAGE`    | `869` | `Core` | `MemoryRequirementListener` | `verifyMemoryLimit`   |     |
| `PRE_IMAGE`    | `859` | `Core` | `BackgroundLayerListener`   | `initFinalFormat`     |     |
| `PRE_IMAGE`    | `849` | `Core` | `TextLayerListener`         | `verifyFreeType`      |     |
| `PRE_IMAGE`    | `839` | `Save` | `ImageFactoryListener`      | `createImage`         | Yes |
| `PRE_IMAGE`    | `829` | `Gif`  | `GifExtractorListener`      | `initExtracted`       |     |
| `PRE_IMAGE`    | `819` | `Gif`  | `MemoryRequirementListener` | `verifyMemoryLimit`   |     |
| `PRE_IMAGE`    | `099` | `Core` | `ImageAwareLayerListener`   | `termFilePointer`     |     |
| `IMAGE`        | `199` | `Gif`  | `ImageFactoryListener`      | `createImage`         | Yes |
| `IMAGE`        | `099` | `Core` | `ImageFactoryListener`      | `createImage`         | Yes |
| `FINISH_IMAGE` | `899` | `Core` | `ImageMetadataListener`     | `addImageMetadatas`   |     |
| `FINISH_IMAGE` | `889` | `Core` | `MemoryRequirementListener` | `addImageExtras`      |     |
| `FINISH_IMAGE` | `879` | `Gif`  | `GifExtractorListener`      | `addImageExtras`      |     |
| `FINISH_IMAGE` | `869` | `Gif`  | `ImageFactoryListener`      | `addImageExtras`      |     |
| `FINISH_IMAGE` | `199` | `Gif`  | `MemoryRequirementListener` | `addImageExtras`      |     |
| `FINISH_IMAGE` | `099` | `Core` | `ImageAwareLayerListener`   | `termFilePointer`     |     |
| `FINISH_IMAGE` | `089` | `Core` | `ImageAwareLayerListener`   | `termImcUri`          |     |
