<?php
/*

	Copyright 2018 Richard McQuiston
	Free for personal, non-commercial use.
	
	Donations Gladly Accepted via PayPal to bigrpromotions@gmail.com
*/

class Inqstats extends Model {
    //http://blog.inqubu.com/inqstats-open-api-published-to-get-demographic-data
    private static $apiKey = '';
    private static $baseURL = 'http://inqstatsapi.inqubu.com';
    public static function getRequest($params, $descr = '') {
        $params['api_key'] = self::$api_key;

        $params = http_build_query($params);
        $url = self::$baseURL . '?' . $params;
        return [
            'results' => file_get_contents($url),
            'description' => trim($descr),
        ];
    }
    public static function getLanguages() {
        return [
            'en' => 'English',
            'de' => 'German',
            'es' => 'Spanish',
            'pt' => 'Portuguese',
            'fr' => 'French',
            'it' => 'Italian',
            'ru' => 'Russian',
            'zh' => 'Chinese',
        ];
    }
    public static function getBigMacindex($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'bigmac_index',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the price for a Big Mac by McDonald's derived from www.bigmacindex.org (unit: USD).");
    }
    public static function getBirthRates($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'birth_rate',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "The average number of birth per year per 1,000 population.");
    }
    public static function getEmissions($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'co2_emissions',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the CO2 emissions in metric tons per person per year.	");
    }
    public static function getCorrputionIndex($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'corruption_index',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the Corruption Perceptions Index (CPI) published by www.transparency.org (scale: 0-100; 0 = high corruption. 100 = low corruption)");
    }
    public static function getDensity($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'density',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the population density of a country (per km²).");
    }
    public static function getDeathRate($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'death_rate',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "The average number of death per year per 1,000 population.");
    }
    public static function getDebts($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'debts',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "The total amount of government borrowings (unit: USD).	");
    }
    public static function getDebtsPercent($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'debts_capita',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "The percentage of government borrowings in relation to the GDP.	");
    }
    public static function getDebtsPerCapita($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'debts_capita',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "The amount of government borrowings per person (unit: USD).	");
    }
    public static function getDiabetes($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'diabetes_prevalence',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "The percentage of people ages 20-79 who have type 1 or type 2 diabetes.");
    }
    public static function getEconomicSectors($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'economic_sectors',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the fraction of the three economic sectors (agriculture = primary, industry = secondary, service = tertiary) of the total economic performance.");
    }
    public static function getEducationExpenditure($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'education_expenditure',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the public expenditure on education (in % of the GDP for a country).");
    }
    public static function getEnergyConsumption($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'electric_energy_consumption',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the amount of electric energy consumed (in kWh/capita)");
    }
    public static function getFirstMarriageMen($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'first_marriage_men',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the age of first marriage for men.");
    }
    public static function getFirstMarriageWomen($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'first_marriage_women',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the age of first marriage for women.");
    }
    public static function getGDPTotal($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'gdp_total',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the total Gross Domestic Product (GDP) for a country (unit: USD).	");
    }
    public static function getGDPCapita($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'gdp_capita',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the Gross Domestic Product per person for a country (unit: USD).");
    }
    public static function getHappinessIndex($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'happiness_index',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the values of the world happiness survey of the UNSDSN. The higher the value, the happier the country.");
    }
    public static function getHealthExpenditure($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'health_expenditure',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the public expenditure on health (in % of the GDP for a country).");
    }
    public static function getInflation($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'inflation',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the annual change of consumer prices (unit: %).");
    }
    public static function getInternetUsers($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'internetuser',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the total number of people that are actively using the internet.");
    }
    public static function getInternetUsersPercent($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'internetusers_percent',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the percentage of people, that are actively using the internet for a country.");
    }
    public static function getJoblessRate($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'internetusers_percent',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "The number of unemployed people in relation to the labor force for a country.	");
    }
    public static function getLifeExpectancy($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'life_expectancy',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "The average number of years a person will live (at birth).");
    }
    public static function getLiteracy($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'literacy_rate',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the percentage of people, that have the ability to read and write by the age of 15.");
    }
    public static function getCellSubscriptions($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'mobile_cellular_subscriptions',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the number of mobile phone subscriptions (per 100 population)	");
    }
    public static function getMurderRate($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'murder_rate',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the number of homicides (per 100,000 population).");
    }
    public static function getNewBusinesses($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'new_businesses_registered',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Number of new limited liability corporations registered in the calendar year.	");
    }
    public static function getPopulation($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'population',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the population of a country.");
    }
    public static function getPopulation0_14($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'population_0_14',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the percentage of people between the age 0 and 14.");
    }
    public static function getPopulation15_64($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'population_15_64',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the percentage of of people between the age 15 and 64.	");
    }
    public static function getPopulation_65_Plus($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'population_over_64',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the percentage of people who are older than 64 years.	");
    }
    public static function getPressFreedom($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'pressfreedom_index',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns an index derived from \"Reporters without borders\" that reflects how free the press of a country is. The lower the value, the better the freedom of press.");
    }
    public static function getResearchExpenditure($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'research_expenditure',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the public expenditures on scientific research (in % of the GDP for a country).	");
    }
    public static function getSize($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'size',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the area of a country (unit: km²).");
    }
    public static function getTaxRevenue($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'tax_revenue',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Compulsory transfers to the central government for public purposes (in % of the GDP for a country).");
    }
    public static function getTaxRevenueTotal($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'tax_revenue_total',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Compulsory transfers to the central government for public purposes (USD).");
    }
    public static function getPopulationUrban($countries = 'us', $years = '', $lang = 'en') {
        $params = [
            'data' => 'urban_population',
            'countries' => $countries,
            'lang' => $lang,
        ];
        if ($years != '') { $params['years'] = $years; }
        return self::getRequest($params, "Returns the percentage of people who live in a city.");
    }
}
