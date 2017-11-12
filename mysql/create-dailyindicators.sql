# MySQL dump 8.16
#
# Host: localhost    Database: IntraDayData
#--------------------------------------------------------
# Server version	3.23.47

#
# Table structure for table 'dailyindicators'
#


DROP TABLE IF EXISTS dailyindicators;
CREATE TABLE dailyindicators(
  ISIN CHAR(15) default NULL,
  Day date default NULL,
  Period integer default NULL,
  AvgPrice float default NULL,
  AvgVol int(11) default NULL,
  StddevPrice float default NULL,
  Ismin tinyint default NULL,
  Ismax tinyint default NULL,
  UNIQUE KEY I_dailyindicators (ISIN, Day, Period)
) ;

