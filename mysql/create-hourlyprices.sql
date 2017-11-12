# MySQL dump 8.16
#
# Host: localhost    Database: stockprices 
#--------------------------------------------------------
# Server version	3.23.47


#
# Table structure for table 'hourlyprices'
#

DROP TABLE IF EXISTS hourlyprices;
CREATE TABLE hourlyprices (
  ISIN char(15) default NULL,
  TimeDate datetime default NULL,
  NbDataPoints integer default NULL,
  Open float default NULL,
  High float default NULL,
  Low float default NULL,
  Close float default NULL,
  Volume bigint(20) default NULL,
  CumVolume bigint(20) default NULL,
  Ratio float default 1,
  UNIQUE KEY I_HourlyPrices (ISIN,TimeDate)
) ;

#
