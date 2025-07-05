<center>
    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width: 600px; margin: 0 auto; font-family: 'Arial', sans-serif; color: #111827; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb;">
        <tr>
            <td style="padding: 30px 20px; text-align: center; background: linear-gradient(135deg, #dc2626, #f97316); border-radius: 8px 8px 0 0;">
                <h1 style="color: #ffffff; font-size: 24px; margin: 0; font-weight: 600;">Domain Deletion Notice</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px 20px; text-align: left;">
                <p style="font-size: 16px; line-height: 1.6; color: #4b5563; margin-bottom: 20px;">Hello {{ $username }},</p>
                
                <p style="font-size: 16px; line-height: 1.6; color: #4b5563; margin-bottom: 20px;">We regret to inform you that your domain <strong>{{ $domainName }}</strong> has been deleted from {{ $webName }} by an administrator.</p>
                
                @if(!empty($reason))
                <div style="background-color: #f9fafb; border-left: 4px solid #dc2626; padding: 15px; margin-bottom: 20px; border-radius: 0 4px 4px 0;">
                    <p style="font-size: 16px; line-height: 1.6; color: #4b5563; margin: 0; display: flex; align-items: flex-start;">
                        <span style="margin-right: 10px; color: #dc2626;">&#9888;</span>
                        <span><strong>Reason for deletion:</strong> <em>{{ $reason }}</em></span>
                    </p>
                </div>
                @endif
                
                @if(isset($records) && count($records) > 0)
                <div style="margin-bottom: 20px;">
                    <h3 style="font-size: 18px; color: #4b5563; margin-bottom: 10px;">Deleted DNS Records:</h3>
                    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse: collapse; margin-bottom: 15px;">
                        <thead>
                            <tr>
                                <th style="padding: 10px; text-align: left; background-color: #f3f4f6; border: 1px solid #e5e7eb; font-size: 14px; color: #374151;">Host</th>
                                <th style="padding: 10px; text-align: left; background-color: #f3f4f6; border: 1px solid #e5e7eb; font-size: 14px; color: #374151;">Type</th>
                                <th style="padding: 10px; text-align: left; background-color: #f3f4f6; border: 1px solid #e5e7eb; font-size: 14px; color: #374151;">Value</th>
                                <th style="padding: 10px; text-align: left; background-color: #f3f4f6; border: 1px solid #e5e7eb; font-size: 14px; color: #374151;">Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                            <tr>
                                <td style="padding: 10px; text-align: left; border: 1px solid #e5e7eb; font-size: 14px; color: #4b5563;">{{ $record['name'] }}</td>
                                <td style="padding: 10px; text-align: left; border: 1px solid #e5e7eb; font-size: 14px; color: #4b5563;">{{ $record['type'] }}</td>
                                <td style="padding: 10px; text-align: left; border: 1px solid #e5e7eb; font-size: 14px; color: #4b5563;">{{ $record['value'] }}</td>
                                <td style="padding: 10px; text-align: left; border: 1px solid #e5e7eb; font-size: 14px; color: #4b5563;">{{ $record['created_at'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <p style="font-size: 14px; color: #6b7280; margin-top: 5px; font-style: italic;">Note: We recommend backing up your DNS configuration for future reference.</p>
                </div>
                @elseif(isset($record))
                <div style="margin-bottom: 20px;">
                    <h3 style="font-size: 18px; color: #4b5563; margin-bottom: 10px;">Deleted DNS Record Details:</h3>
                    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse: collapse; margin-bottom: 15px;">
                        <tr>
                            <th style="padding: 10px; text-align: left; background-color: #f3f4f6; border: 1px solid #e5e7eb; font-size: 14px; color: #374151; width: 30%;">Host</th>
                            <td style="padding: 10px; text-align: left; border: 1px solid #e5e7eb; font-size: 14px; color: #4b5563;">{{ $record['name'] }}</td>
                        </tr>
                        <tr>
                            <th style="padding: 10px; text-align: left; background-color: #f3f4f6; border: 1px solid #e5e7eb; font-size: 14px; color: #374151;">Type</th>
                            <td style="padding: 10px; text-align: left; border: 1px solid #e5e7eb; font-size: 14px; color: #4b5563;">{{ $record['type'] }}</td>
                        </tr>
                        <tr>
                            <th style="padding: 10px; text-align: left; background-color: #f3f4f6; border: 1px solid #e5e7eb; font-size: 14px; color: #374151;">Value</th>
                            <td style="padding: 10px; text-align: left; border: 1px solid #e5e7eb; font-size: 14px; color: #4b5563;">{{ $record['value'] }}</td>
                        </tr>
                        <tr>
                            <th style="padding: 10px; text-align: left; background-color: #f3f4f6; border: 1px solid #e5e7eb; font-size: 14px; color: #374151;">Created At</th>
                            <td style="padding: 10px; text-align: left; border: 1px solid #e5e7eb; font-size: 14px; color: #4b5563;">{{ $record['created_at'] }}</td>
                        </tr>
                    </table>
                </div>
                @endif
                
                <p style="font-size: 16px; line-height: 1.6; color: #4b5563; margin-bottom: 20px;">If you believe this was done in error or have any questions, please contact our support team.</p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ url('/home') }}" style="display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #2563eb, #7c3aed); color: #ffffff; text-decoration: none; font-weight: 600; border-radius: 9999px; font-size: 16px;">Go to Dashboard</a>
                </div>
                
                <p style="font-size: 16px; line-height: 1.6; color: #4b5563; margin-bottom: 20px;">Thank you for your understanding.</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px; text-align: center; background-color: #f9fafb; border-top: 1px solid #e5e7eb; border-radius: 0 0 8px 8px;">
                <p style="font-size: 14px; color: #6b7280; margin: 0;">&copy; {{ date('Y') }} {{ $webName }}. All rights reserved.</p>
            </td>
        </tr>
    </table>
</center> 