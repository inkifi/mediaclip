<?php
use Mangoit\MediaclipHub\Model\Mediaclip;
/**
 * 2019-02-26
 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pureprint::_p()
 * @used-by \Inkifi\Mediaclip\H\AvailableForDownload\Pwinty::_p()
 * @param string $id  A string like «4cbe2c38-fa9e-4760-966f-f42f021bef84»
 * @return array(string => mixed)
 */
function ikf_m_project_details($id) {
	$m = df_new_om(Mediaclip::class); /** @var Mediaclip $m */
	$m->load($id, 'project_id');
	return df_json_decode($m['project_details']);
}


