# MySQL dump 8.16
#
# Host: localhost    Database: IntraDayData
#--------------------------------------------------------
# Server version	3.23.47

#
# Table structure for table 'monthlyindicators'
#


DROP TABLE IF EXISTS monthlyindicators;
CREATE TABLE monthlyindicators(
  ISIN CHAR(15) default NULL,
  Month CHAR(7) default NULL,
  Period integer default NULL,
  AvgPrice float default NULL,
  AvgVol int(11) default NULL,
  StddevPrice float default NULL,
  Ismin tinyint default NULL,
  Ismax tinyint default NULL,
  UNIQUE KEY I_monthlyindicators (ISIN, Month, Period)
) ;

