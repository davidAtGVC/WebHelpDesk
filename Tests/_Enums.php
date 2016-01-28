<?php

defined('SYSTEM_PATH') || exit("SYSTEM_PATH not found.");

abstract class Qualifier_Operation
{
	const OP = 'qualifier';
    const Equal = '=';
    const NotEqual = '!=';
	const LessThan = '<';
	const LessThanEqual = '<=';
	const GreaterThan = '>';
	const GreaterThanEqual = '>=';
	const Like = 'like';
	const InsensitiveLike = 'caseInsensitiveLike';
}

abstract class Ticket_Fields
{
	const assets = 'assets';
	const assignToCreatingTech = 'assignToCreatingTech';
	const bccAddresses = 'bccAddresses';
	const ccAddressesForTech = 'ccAddressesForTech';
	const clientReporter = 'clientReporter';
	const clientTech = 'clientTech';
	const department = 'department';
	const detail = 'detail';
	const emailBcc = 'emailBcc';
	const emailCc = 'emailCc';
	const emailClient = 'emailClient';
	const emailGroupManager = 'emailGroupManager';
	const emailTech = 'emailTech';
	const emailTechGroupLevel = 'emailTechGroupLevel';
	const location = 'location';
	const prioritytype = 'prioritytype';
	const problemtype = 'problemtype';
	const reportDateUtc = 'reportDateUtc';
	const room = 'room';
	const statustype = 'statustype';
	const subject = 'subject';
}
