#ip2location

Based on IP2Location PHP API with extra downloader tool

## Build Status

[![Build Status](https://travis-ci.com/SmartFrame-Technologies/ip2location.svg?token=6h6rgvxfiMqi9o6VznZs&branch=master)](https://travis-ci.com/SmartFrame-Technologies/ip2location)

## Basic usage

Use static factory to get Exchanger. It requires you to give url with token and packages you desire to download.
```
$dbExchanger = DatabaseExchangeFactory::create($downloadUrl, $package)
```

Then specify file path:

 ```
 $dbExchanger->exchange('folder/ipDatabase.bin');
```


### Usage with cache 

This tool can also use S3 as cache to store DB for many server instances.
```
$dbExchanger = DatabaseExchangeFactory::createWithS3Cache(
                           $downloadUrl,
                           $packages,
                           $s3PhpClient, //AWS S3 Client
                           $s3Bucket,
                           $s3Prefix //folder name
                       );
```

Then specify file path:

 ```
 $dbExchanger->exchange('folder/ipDatabase.bin');
```

## License

Copyright 2020 SmartFrame Technologies

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at:

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.