<?
require_once('PASL/Data/GET.php');
require_once('PASL/Web/Template/Code.php');

namespace PASL\Web;

class Pagination extends Code
{
	protected $PaddingLeft = 10;
	protected $PaddingRight = 10;
	protected $RowCount = 0;
	protected $PerPage = 6;
	protected $CleanURLID = 'page';
	protected $DefaultPage = 1;
	protected $Page = 1;
	
	protected $NextQueryArray = Array();
	protected $PrevQueryArray = Array();
	protected $PageQueryArray = Array();

	public function __construct()
	{
		$this->Page = ($_GET[$this->GETVariableName]) ? $_GET[$this->GETVariableName] : $this->DefaultPage;
	}

	public function setPerPage($PerPage)
	{
		$this->PerPage = $PerPage;
	}

	public function setPaddingLeft($PaddingLeft)
	{
		$this->PaddingLeft = $PaddingLeft;
	}

	public function setPaddingRight($PaddingRight)
	{
		$this->PaddingRight = $PaddingRight;
	}

	public function setRowCount($RowCount)
	{
		$this->RowCount = $RowCount;
	}

	public function setGETVariableName($GETVariableName)
	{
		$this->GETVariableName = $GETVariableName;
	}

	public function setDefaultPage($DefaultPage)
	{
		$this->DefaultPage = $DefaultPage;
	}

	public function setPage($Page)
	{
		$this->Page = $Page;
	}

	public function getStartLimit()
	{
		$start_limit = $this->PerPage * ($this->Page - 1);
		return $start_limit;
	}

	public function getEndLimit()
	{
		$end_limit = ($this->PerPage * ($this->Page - 1)) + $this->PerPage;
		return $end_limit;
	}

	public function getPage()
	{
		return $this->Page;
	}

	public function getPerPage()
	{
		return $this->PerPage;
	}
	
	public function setNextQueryArray($NextQueryArray)
	{
		$this->NextQueryArray = $NextQueryArray;
	}
	
	public function setPrevQueryArray($PrevQueryArray)
	{
		$this->PrevQueryArray = $PrevQueryArray;
	}

	public function setPageQueryArray($PageQueryArray)
	{
		$this->PageQueryArray = $PageQueryArray;
	}

	public function __toString()
	{
		$row_count = $this->RowCount;

		$page = $this->Page;
		$page_count = ceil($row_count / $this->PerPage);

		$page_padding_left = $this->PaddingLeft;
		$page_padding_right = $this->PaddingRight;

		$start_page = ($page - $page_padding_left <= 0) ? 1 : ($page - $page_padding_left);
		$end_page = ($page + $page_padding_right >= $page_count) ? $page_count : ($page + $page_padding_right);

		$next_page = ( ($page + 1) < $page_count) ? ($page + 1) : $page_count;
		$prev_page = ($page - 1 != 0) ? ($page - 1) : 1;

		$PaginationInfo = Array();
		for($i=$start_page; $i <= $end_page; $i++)
		{
			$Page = new \stdClass;
			$Page->Number = $i;
			$Page->Selected = ($this->Page == $i) ? true : false;
			$Page->GETQueryString = PASL_Data_GET::buildQueryString(array_merge( $this->PageQueryArray, Array(''.$this->GETVariableName.'' => $i)));

			$PaginationInfo[] = $Page;
		}


		$NextPageQuery = PASL_Data_GET::buildQueryString
		(
			array_merge($this->NextQueryArray, Array(''.$this->GETVariableName.'' => $next_page))
		);


		$PrevPageQuery = PASL_Data_GET::buildQueryString
		(
			array_merge($this->PrevQueryArray, Array(''.$this->GETVariableName.'' => $prev_page))
		);

		$this->SetVariables(Array('PaginationPages' => $PaginationInfo, 'NextPageQuery' => $NextPageQuery, 'PrevPageQuery' => $PrevPageQuery));

		return parent::__toString();
	}
}
?>